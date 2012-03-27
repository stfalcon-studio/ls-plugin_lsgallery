var ls = ls || {};

ls.comments  = ls.comments || {};

if (ls.comments) {
    ls.comments.options.type.image = {
        url_add: aRouter['gallery'] + 'ajaxaddcomment/',
        url_response: aRouter['gallery'] + 'ajaxresponsecomment/'
    };
}

ls.favourite  = ls.favourite || {};

if (ls.favourite) {
    ls.favourite.options.type.image = {
        url: aRouter['galleryajax'] + 'favourite/',
        targetName: 'idImage'
    };
}

ls.vote  = ls.vote || {};

if (ls.vote) {
    ls.vote.options.type.image = {
        url: aRouter['galleryajax'] + 'vote/',
        targetName: 'idImage'
    };
}

ls.blocks  = ls.blocks || {};

if (ls.blocks) {
    ls.blocks.options.type.block_gallery_item_new = {
        url: aRouter['galleryajax'] + 'getnewimages/'
    };
    ls.blocks.options.type.block_gallery_item_best = {
        url: aRouter['galleryajax'] + 'getbestimages/'
    };
}


ls.gallery = (function ($) {
    this.idLast = 0;
    this.isLoading = false;
    this.swfu;
    this.initSwfUpload = function (opt) {
        opt = opt || {};
        opt.debug = false;
        opt.button_width = 205;
        opt.button_height = 34;
        opt.upload_url =  aRouter['galleryajax'] + "upload";
        opt.button_image_url = DIR_WEB_LSGALLERY_SKIN + "images/add-btn.png";
        opt.button_text = null;
        opt.button_text_style = null;
        opt.button_window_mode = "window";
        opt.button_placeholder_id = 'images-start-upload';
        opt.file_dialog_complete_handler = this.handlerFileDialogComplete;
        opt.upload_success_handler = this.handlerUploadSuccess;
        opt.upload_complete_handler = this.handlerUploadComplete;
        opt.upload_progress_handler = this.handlerUploadProgress;
        $(ls.swfupload).bind('load', function () {
            this.swfu = ls.swfupload.init(opt);
        }.bind(this));
        ls.swfupload.loadSwf();
    };
    this.handlerUploadProgress = function (file, bytesLoaded) {
        var percent = Math.ceil((bytesLoaded / file.size) * 100);
        jQuery('#gallery_image_empty_progress').text(file.name + ': ' + (percent == 100 ? 'resize..' : percent +'%'));
    };
    this.handlerFileDialogComplete = function(numFilesSelected, numFilesQueued) {
        var stats = this.getStats();
        if (stats.files_queued == numFilesSelected && numFilesSelected > 0) {
            this.startUpload();
            ls.gallery.addImageEmpty();
        }

	};
    this.handlerUploadSuccess = function(file, serverData) {
        ls.gallery.addImage(jQuery.parseJSON(serverData));
        var next = this.getStats().files_queued;
		if (next > 0) {
			this.startUpload();
            ls.gallery.addImageEmpty();
		}
		$(this).trigger('eUploadSuccess',[file, serverData]);
	};
    this.swfHandlerUploadComplete = function(e, file, next) {
        return;
    };
    this.addImageEmpty = function () {
        var template = '<li id="gallery_image_empty"><img src="' + DIR_STATIC_SKIN + '/images/loader.gif'+'" alt="image" style="margin-left: 35px;margin-top: 20px;" />'
        + '<div id="gallery_image_empty_progress" style="height: 60px;width: 350px;padding: 3px;border: 1px solid #DDDDDD;"></div><br /></li>';
        jQuery('#swfu_images').prepend(template);
    };
    this.addImage = function (response) {
        jQuery('#gallery_image_empty').remove();
        if (!response.bStateError) {
            var template = '<li id="image_' + response.id + '"><img class="100-image" src="' + response.file + '" alt="image" />' 
            + '<label class="description">' + ls.lang.get('lsgallery_image_description') + '</label><br/>'
            + '<textarea onBlur="ls.gallery.setImageDescription('+response.id+', this.value)"></textarea><br />'
            + '<label class="tags">' + ls.lang.get('lsgallery_image_tags') + '</label><br/>'
            + '<input type="text" class="autocomplete-image-tags" onBlur="ls.gallery.setImageTags('+response.id+', this.value)"/><br/>'
            + '<div class="options-line"><span id="image_preview_state_' + response.id + '" class="photo-preview-state"><a href="javascript:ls.gallery.setPreview(' + response.id + ')" class="mark-as-preview">' + ls.lang.get('lsgallery_album_set_image_cover') + '</a></span>'
            + '<a href="javascript:ls.gallery.deleteImage(' + response.id + ')" class="image-delete">' + ls.lang.get('lsgallery_album_image_delete') + '</a></div></li>';
            jQuery('#swfu_images').prepend(template);
            ls.autocomplete.add($(".autocomplete-image-tags"), aRouter['galleryajax']+'autocompleteimagetag/', true);
            ls.msg.notice(response.sMsgTitle, response.sMsg);
        } else {
            ls.msg.error(response.sMsgTitle, response.sMsg);
        }
    };

    this.deleteImage = function (id) {
        if (!confirm(ls.lang.get('lsgallery_album_image_delete_confirm'))) {
            return;
        }
        ls.ajax(aRouter['galleryajax'] + 'deleteimage', {
            'id': id
        }, function (response) {
            if (!response.bStateError) {
                jQuery('#image_' + id).remove();
                ls.msg.notice(response.sMsgTitle, response.sMsg);
            } else {
                ls.msg.error(response.sMsgTitle, response.sMsg);
            }
        });
    };

    this.setPreview = function (id) {
        ls.ajax(aRouter['galleryajax'] + 'markascover', {
            'id': id
        }, function (response) {
            if (!response.bStateError) {
                $('.marked-as-preview').each(function (index, el) {
                    jQuery(el).removeClass('marked-as-preview');
                    var tmpId = $(el).attr('id').slice($(el).attr('id').lastIndexOf('_') + 1);
                    $('#image_preview_state_' + tmpId).html('<a href="javascript:ls.gallery.setPreview(' + tmpId + ')" class="mark-as-preview">' + ls.lang.get('lsgallery_album_set_image_cover') + '</a>');
                });
                $('#image_'+id).addClass('marked-as-preview');
                $('#image_preview_state_' + id).html(ls.lang.get('lsgallery_album_image_cover'));
            } else {
                ls.msg.error(response.sMsgTitle, response.sMsg);
            }
        });

    };
    
    this.setImageDescription = function (id, text) {
        if (!text) {
            return;
        }
        jQuery('#image_' + id + ' label.description').html(ls.lang.get('lsgallery_image_description'));
        ls.ajax(aRouter['galleryajax'] + 'setimagedescription', {
            'id': id,
            'text': text
        },  function (result) {
            if (result.bStateError) {
                ls.msg.error('Error', 'Please try again later');
            } else {
                jQuery('#image_' + id + ' label.description').html(ls.lang.get('lsgallery_image_description_updated'));
            }
        });
    };
    
    this.setImageTags = function (id, text) {
        if (jQuery('ul.ui-autocomplete').css('display') === 'block') {
            return;
        }
        if (!text) {
            return;
        }
        jQuery('#image_' + id + ' label.tags').html(ls.lang.get('lsgallery_image_tags'));
        ls.ajax(aRouter['galleryajax'] + 'setimagetags', {
            'id': id,
            'tags': text
        },  function (result) {
            if (result.bStateError) {
                ls.msg.error('Error', 'Please try again later');
            } else {
                jQuery('#image_' + id + ' label.tags').html(ls.lang.get('lsgallery_image_tags_updated'));
            }
        });
    };
    
    this.changeMark = function(idImage, idUser, status, a) {
        ls.ajax(aRouter['galleryajax'] + 'changemark', {
            'idImage': idImage,
            'idUser': idUser,
            'status': status
        },  function (response) {
            if (response.bStateError) {
                ls.msg.error(response.sMsgTitle, response.sMsg);
            } else {
                jQuery('#current-image-user').remove();
                ls.msg.notice(response.sMsgTitle, response.sMsg);
            }
        });
    }
    
    this.removeMark = function(idImage, idUser, a) {
        ls.ajax(aRouter['galleryajax'] + 'removemark', {
            'idImage': idImage,
            'idUser': idUser
        },  function (response) {
            if (response.bStateError) {
                ls.msg.error(response.sMsgTitle, response.sMsg);
            } else {
                jQuery('#target-' + idUser).remove();
                jQuery('#marked-user-' + idUser).remove();
                jQuery('#current-image-user').remove();
                ls.msg.notice(response.sMsgTitle, response.sMsg);
            }
        });
    }
    
    
    return this;
}).call(ls.gallery || {},jQuery);

jQuery('document').ready(function(){
    ls.autocomplete.add($(".autocomplete-image-tags"), aRouter['galleryajax']+'autocompleteimagetag/', true);
    
    if (jQuery('a.gal-expend').length) {
        jQuery('a.gal-expend').fancybox();
    };
    
    jQuery('div.image-marker').live('mouseover', function(){
        jQuery(this).find('div.marker-wrap').first().show();
    });
    
    jQuery('div.image-marker').live('mouseout', function(){
        jQuery(this).find('div.marker-wrap').first().hide();
    });
    
    jQuery('#selected-people li').live('mouseover', function(){
        var id = jQuery(this).attr('id').replace('target-', '');
        jQuery('#marked-user-' + id + ' div.marker-wrap').show();
    });
    
    jQuery('#selected-people li').live('mouseout', function(){
        var id = jQuery(this).attr('id').replace('target-', '');
        jQuery('#marked-user-' + id + ' div.marker-wrap').hide();
    });
    
    jQuery('#gallery-reload').click(function(event){
        event.preventDefault();
        ls.ajax(aRouter['galleryajax'] + 'getrandomimages', {},
            function (result) {
                if (result.sHtml) {
                    jQuery('#block-random-images').html(result.sHtml);
                }
            });
    });
    
    jQuery('[id^="block_gallery_item"]').click(function(){
        ls.blocks.load(this, 'block_gallery');
        return false;
    });
    
    jQuery('#stream-images a.tooltiped').tooltip({
        position: "bottom center",
        offset: [-40, 0]
    });
    
    // Поиск по тегам
    $('#tag__image_search_form').submit(function(){
        window.location = aRouter['gallery'] + 'tag/' + $('#tag_search').val()+'/';
        return false;
    });
    
    jQuery('#gallery-slideshow').click(function(event){
        event.preventDefault();
        jQuery('a.image-slideshow').fancybox({
            arrows:true,
            helpers : {
                title : {
                    type : 'inside'
                },
                buttons	: {}
            }
        });
        jQuery('a.image-slideshow').first().trigger('click');
    });
    
    if (jQuery('#select-friends').length) {
        var ias = jQuery('#image img').imgAreaSelect({
            instance: true,
            handles: true,
            minHeight: 100,
            minWidth: 100,
            disable: true
        });
    }
    
    jQuery('#mark').click(function(event){
        event.preventDefault();
        if (jQuery('div.mark-name.current').length) {
            cancelMarkFriend();
        } else {
            jQuery('#image img').addClass('select-pic');
            jQuery('#select-people-notice').css('opacity', 0).slideDown(70).animate({
                opacity:1
            }, 200);
            var name = jQuery('div.mark-name').clone();
            name.addClass('current').show();
            var acp = name.find('input.autocomplete-friend').first();
            ls.autocomplete.add(acp, aRouter['ajax']+'autocompleter/user/', false);
            jQuery('.imgareaselect-handle:last').parent('div').first().append(name);
            ias.setOptions({
                enable: true
            });
            $('html, body').animate({
                scrollTop: $("#content").offset().top
            }, 600);
        }
        
    });
    
    jQuery('div.mark-name.current .cancel-selected-friend').live('click',function(event){
        event.preventDefault();
        hideMarkFriend();
    });
    
    jQuery('div.mark-name.current .submit-selected-friend').live('click', function(event){
        event.preventDefault();
        var selection = ias.getSelection();
        var login = jQuery('div.mark-name.current .autocomplete-friend').val();
        if (!selection.height || !login) {
            return;
        }
        var idImage = jQuery('#image img').attr('id');
        ls.ajax(aRouter['galleryajax'] + 'markfriend', {
            'idImage': idImage,
            'login': login,
            'selection': selection
        },  function (response) {
            if (response.bStateError) {
                ls.msg.error(response.sMsgTitle, response.sMsg);
            } else {
                var li = '<li id="target-' + response.idUser + '" class="selected-new">'+
                '<a class="user" href="' + response.sPath + '">' + login + '</a>'+
                '<a href="#" class="remove" onclick="ls.gallery.removeMark(' + idImage +', ' + response.idUser + ', this); return false;"></a>'+
                '</li>';
                jQuery('#selected-people').append(li);
                var div = '<div class="image-marker" id="marked-user-' + response.idUser + '" style="top: '+ selection.y1 +'px; left: ' +
                selection.x1 + 'px; width: ' + selection.width + 'px; height: ' + selection.height + 'px;">' +
                '<div class="marker-wrap" style="width: '+ selection.width + 'px; height: '+ selection.height + 'px;  display: none;">' + 
                '<div class="marker-inside" style="width: '+ (selection.width -2) + '}px; height: '+ (selection.height -2) + 'px"></div>'+
                '<div class="user-href-wrap"><a class="user" href="' + response.sPath + '">' + login + '</a></div>'+
                '</div></div>';
                jQuery('#image').append(div);
                hideMarkFriend();
                ls.msg.notice(response.sMsgTitle, response.sMsg);
            }
        }); 
    })
    
    jQuery('#image-mark-ready').live('click', function(event){
        event.preventDefault();
        cancelMarkFriend();
    })
    function cancelMarkFriend() {
        jQuery('#image img').removeClass('select-pic');
        jQuery('#select-people-notice').animate({
            opacity:0
        }, 0).slideUp(30);
        jQuery('div.mark-name.current').remove();
        ias.setOptions({
            disable: true,
            hide: true
        });
    }
    function hideMarkFriend(){
        jQuery('div.mark-name.current input').val('');
        ias.setOptions({
            hide: true
        });
    }
    
    jQuery('#image img.select-pic').live('click', function(event){
        event.preventDefault();
        clickSelect(event);
    })
    
    jQuery('.imgareaselect-outer').live('click', function(event){
        event.preventDefault();
        clickSelect(event);
    })
    
    function clickSelect(event) {
        var offset = jQuery('#image img.select-pic').offset();
        var X1, X2, Y1, Y2;
        if ((event.pageX - offset.left - 50) > 0 ) {
            X1 = event.pageX - offset.left - 50;
            if ((event.pageX - offset.left + 50) < jQuery('#image img.select-pic').width()) {
                X2 = (event.pageX - offset.left + 50);
            } else {
                X2 = jQuery('#image img.select-pic').width();
                if ((X2 - 100) > 0 ) {
                    X1 = X2 - 100;
                } else {
                    X1 = 0;
                    X2 = 100;
                }
            }
        } else {
            X1 = 0;
            X2 = 100
        }
        
        if ((event.pageY - offset.top - 50) > 0 ) {
            Y1 = event.pageY - offset.top - 50;
            if ((event.pageY - offset.top + 50) < jQuery('#image img.select-pic').height()) {
                Y2 = (event.pageY - offset.top + 50);
            } else {
                Y2 = jQuery('#image img.select-pic').height();
                if ((Y2 - 100) > 0 ) {
                    Y1 = Y2 - 100;
                } else {
                    Y1 = 0;
                    Y2 = 100;
                }
            }
        } else {
            Y1 = 0;
            Y2 = 100;
        }
        
        
        ias.setSelection(X1, Y1, X2, Y2);
        ias.setOptions({
            show: true
        });
    }
});