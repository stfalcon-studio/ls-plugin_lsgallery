ls.comments  = ls.comments || {};

if (ls.comments) {
    ls.comments.options.type.image = {
        url_add: aRouter.gallery + 'ajaxaddcomment/',
        url_response: aRouter.gallery + 'ajaxresponsecomment/'
    };
}

ls.favourite  = ls.favourite || {};

if (ls.favourite) {
    ls.favourite.options.type.image = {
        url: aRouter.galleryajax + 'favourite/',
        targetName: 'idImage'
    };
}

ls.vote  = ls.vote || {};

if (ls.vote) {
    ls.vote.options.type.image = {
        url: aRouter.galleryajax + 'vote/',
        targetName: 'idImage'
    };
}

ls.blocks  = ls.blocks || {};

if (ls.blocks) {
    ls.blocks.options.type.block_gallery_item_new = {
        url: aRouter.galleryajax + 'getnewimages/'
    };
    ls.blocks.options.type.block_gallery_item_best = {
        url: aRouter.galleryajax + 'getbestimages/'
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
        opt.upload_url =  aRouter.galleryajax + "upload";
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
    // process image loading, percentage
    this.handlerUploadProgress = function (file, bytesLoaded) {
        var percent = Math.ceil((bytesLoaded / file.size) * 100);
        jQuery('#gallery_image_empty_progress').text(file.name + ': ' + (percent == 100 ? 'resize..' : percent +'%'));
    };
    // dialog uplaod complete, load image
    this.handlerFileDialogComplete = function(numFilesSelected, numFilesQueued) {
        var stats = this.getStats();
        if (stats.files_queued == numFilesSelected && numFilesSelected > 0) {
            this.startUpload();
            ls.gallery.addImageEmpty();
        }

    };
    // upload success, place image
    this.handlerUploadSuccess = function (file, serverData) {
        ls.gallery.addImage(jQuery.parseJSON(serverData));
        var next = this.getStats().files_queued;
        if (next > 0) {
            this.startUpload();
            ls.gallery.addImageEmpty();
        }
        $(this).trigger('eUploadSuccess',[file, serverData]);
    };
    // place empty upload in html
    this.addImageEmpty = function () {
        var template = '<li id="gallery_image_empty"><img src="' + DIR_STATIC_SKIN + '/images/loader.gif'+'" alt="image" style="margin-left: 35px;margin-top: 20px;" />'
            + '<div id="gallery_image_empty_progress" style="height: 60px;width: 350px;padding: 3px;border: 1px solid #DDDDDD;"></div><br /></li>';
        jQuery('#swfu_images').prepend(template);
    };
    // place uploaded image in html
    this.addImage = function (response) {
        jQuery('#gallery_image_empty').remove();
        if (!response.bStateError) {
            var template = '<li id="image_' + response.id + '"><img class="image-100" src="' + response.file + '" alt="image" />'
                + '<label class="description">' + ls.lang.get('lsgallery_image_description') + '</label><br/>'
                + '<textarea onBlur="ls.gallery.setImageDescription(' + response.id + ', this.value)"></textarea><br />'
                + '<label class="tags">' + ls.lang.get('lsgallery_image_tags') + '</label><br/>'
                + '<input type="text" class="autocomplete-image-tags" onBlur="ls.gallery.setImageTags(' + response.id + ', this.value)"/><br/>'
                + '<div class="options-line"><span class="photo-preview-state"><span id="image_preview_state_' + response.id + '">'
                + '<a href="javascript:ls.gallery.setPreview(' + response.id + ')" class="mark-as-preview">' + ls.lang.get('lsgallery_album_set_image_cover') + '</a></span><br/>'
                + '<a href="javascript:ls.gallery.toggleForbidComment(' + response.id + ')" class="image-comment">' + ls.lang.get('lsgallery_set_forbid_comments') + '</a></span>'
                + '<a href="javascript:ls.gallery.deleteImage(' + response.id + ')" class="image-delete">' + ls.lang.get('lsgallery_album_image_delete') + '</a>'
                + '</div></li>';
            jQuery('#swfu_images').prepend(template);
            ls.autocomplete.add($(".autocomplete-image-tags"), aRouter['galleryajax'] + 'autocompleteimagetag/', true);
            ls.msg.notice(response.sMsgTitle, response.sMsg);
        } else {
            ls.msg.error(response.sMsgTitle, response.sMsg);
        }
    };
    // process delete image
    this.deleteImage = function (id) {
        if (!confirm(ls.lang.get('lsgallery_album_image_delete_confirm'))) {
            return;
        }
        ls.ajax(aRouter.galleryajax + 'deleteimage', {
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
    // set image as album preview
    this.setPreview = function (id) {
        ls.ajax(aRouter.galleryajax + 'markascover', {
            'id': id
        }, function (response) {
            if (!response.bStateError) {
                $('.marked-as-preview').each(function (index, el) {
                    jQuery(el).removeClass('marked-as-preview');
                    var tmpId = $(el).attr('id').slice($(el).attr('id').lastIndexOf('_') + 1);
                    $('#image_preview_state_' + tmpId).html('<a href="javascript:ls.gallery.setPreview(' + tmpId + ')" class="mark-as-preview">' + ls.lang.get('lsgallery_album_set_image_cover') + '</a>');
                });
                $('#image_' + id).addClass('marked-as-preview');
                $('#image_preview_state_' + id).html(ls.lang.get('lsgallery_album_image_cover'));
            } else {
                ls.msg.error(response.sMsgTitle, response.sMsg);
            }
        });

    };

    // forbid image comment
    this.toggleForbidComment = function (id) {
        ls.ajax(aRouter.galleryajax + 'toggleforbidcomment', {
            'id': id
        }, function (response) {
            if (!response.bStateError) {
                $('#image_' + id + ' a.image-comment').html(response.sText);
            } else {
                ls.msg.error(response.sMsgTitle, response.sMsg);
            }
        });

    };
    // set image descr
    this.setImageDescription = function (id, text) {
        if (!text) {
            return;
        }
        jQuery('#image_' + id + ' label.description').html(ls.lang.get('lsgallery_image_description'));
        ls.ajax(aRouter.galleryajax + 'setimagedescription', {
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
    // set image tags
    this.setImageTags = function (id, text) {
        if (jQuery('ul.ui-autocomplete').css('display') === 'block') {
            return;
        }
        if (!text) {
            return;
        }
        jQuery('#image_' + id + ' label.tags').html(ls.lang.get('lsgallery_image_tags'));
        ls.ajax(aRouter.galleryajax + 'setimagetags', {
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
    // chaneg image people mark
    this.changeMark = function (idImage, idUser, status, a) {
        ls.ajax(aRouter.galleryajax + 'changemark', {
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
    };
    // remove image user mark
    this.removeMark = function (idImage, idUser, a) {
        ls.ajax(aRouter.galleryajax + 'removemark', {
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
    };


    return this;
}).call(ls.gallery || {}, jQuery);
var ias = null;
jQuery('document').ready(function(){
    // autocomplete for image tags
    ls.autocomplete.add(jQuery(".autocomplete-image-tags"), aRouter.galleryajax + 'autocompleteimagetag/', true);
    // init fancybox for gallery
    if (jQuery('a.gal-expend').length) {
        jQuery('a.gal-expend').fancybox();
    };
    // show marker on mark over
    jQuery('div.image-marker').live('mouseover', function () {
        jQuery(this).find('div.marker-wrap').first().show();
    });
    // hide marker on mark out
    jQuery('div.image-marker').live('mouseout', function () {
        jQuery(this).find('div.marker-wrap').first().hide();
    });
    // show marker on people over
    jQuery('#selected-people li').live('mouseover', function () {
        var id = jQuery(this).attr('id').replace('target-', '');
        jQuery('#marked-user-' + id + ' div.marker-wrap').show();
    });
    // hide marker on people out
    jQuery('#selected-people li').live('mouseout', function () {
        var id = jQuery(this).attr('id').replace('target-', '');
        jQuery('#marked-user-' + id + ' div.marker-wrap').hide();
    });
    // change random images
    jQuery('#gallery-reload').live('click', function (event) {
        event.preventDefault();
        ls.ajax(aRouter.galleryajax + 'getrandomimages', {},
            function (result) {
                if (result.sHtml) {
                    jQuery('#block-random-images').html(result.sHtml);
                }
            });
    });
    // change tab in block
    jQuery('[id^="block_gallery_item"]').live('click', function () {
        ls.blocks.load(this, 'block_gallery');
        return false;
    });
    // add tooltip
    jQuery('#stream-images a.tooltiped').tooltip({
        position: "bottom center",
        offset: [-40, 0]
    });

    // Поиск по тегам
    jQuery('#tag__image_search_form').live('submit', function () {
        window.location = aRouter.gallery + 'tag/' + jQuery('#tag_search').val() + '/';
        return false;
    });
    // show slideshow
    jQuery('#gallery-slideshow').live('click', function (event) {
        event.preventDefault();
        jQuery('a.image-slideshow').fancybox({
            arrows: true,
            helpers : {
                title : {
                    type : 'inside'
                },
                buttons	: {}
            }
        });
        jQuery('a.image-slideshow').first().trigger('click');
    });
    // init imgAreaSelect
    function initMark() {
        ias = jQuery('#image img').imgAreaSelect({
            instance: true,
            handles: true,
            minHeight: 100,
            minWidth: 100,
            disable: true
        });
    }
    //cancel marking
    function cancelMarkFriend() {
        jQuery('#image img').removeClass('select-pic');
        jQuery('#select-people-notice').animate({
            opacity: 0
        }, 0).slideUp(30);
        jQuery('div.mark-name.current').remove();
        ias.setOptions({
            disable: true,
            hide: true
        });
    }
    // hide markind
    function hideMarkFriend() {
        jQuery('div.mark-name.current input').val('');
        ias.setOptions({
            hide: true
        });
    }

    // process clicking on image while mark
    function clickSelect(event) {
        var offset = jQuery('#image img.select-pic').offset(),
            X1,
            X2,
            Y1,
            Y2;
        if ((event.pageX - offset.left - 50) > 0 ) {
            X1 = event.pageX - offset.left - 50;
            if ((event.pageX - offset.left + 50) < jQuery('#image img.select-pic').width()) {
                X2 = (event.pageX - offset.left + 50);
            } else {
                X2 = jQuery('#image img.select-pic').width();
                if ((X2 - 100) > 0) {
                    X1 = X2 - 100;
                } else {
                    X1 = 0;
                    X2 = 100;
                }
            }
        } else {
            X1 = 0;
            X2 = 100;
        }

        if ((event.pageY - offset.top - 50) > 0 ) {
            Y1 = event.pageY - offset.top - 50;
            if ((event.pageY - offset.top + 50) < jQuery('#image img.select-pic').height()) {
                Y2 = (event.pageY - offset.top + 50);
            } else {
                Y2 = jQuery('#image img.select-pic').height();
                if ((Y2 - 100) > 0) {
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
    // imgAreaSelect for people mark
    if (jQuery('#select-friends').length) {
        initMark();
    }

    // show mark
    jQuery('#mark').live('click', function (event) {
        event.preventDefault();
        if (jQuery('div.mark-name.current').length) {
            cancelMarkFriend();
        } else {
            jQuery('#image img').addClass('select-pic');
            jQuery('#select-people-notice').css('opacity', 0).slideDown(70).animate({
                opacity: 1
            }, 200);
            var name = jQuery('div.mark-name').clone(),
                acp = name.find('input.autocomplete-friend').first();
            name.addClass('current').show();
            ls.autocomplete.add(acp, aRouter.ajax + 'autocompleter/user/', false);
            jQuery('.imgareaselect-handle:last').parent('div').first().append(name);
            ias.setOptions({
                enable: true
            });
            jQuery('html, body').animate({
                scrollTop: $("#content").offset().top
            }, 600);
        }

    });
    // cancel set mark
    jQuery('div.mark-name.current .cancel-selected-friend').live('click', function (event) {
        event.preventDefault();
        hideMarkFriend();
    });
    // submit mark
    jQuery('div.mark-name.current .submit-selected-friend').live('click', function (event) {
        event.preventDefault();
        var selection = ias.getSelection(),
            login = jQuery('div.mark-name.current .autocomplete-friend').val(),
            idImage = jQuery('#image img').attr('id');
        if (!selection.height || !login) {
            return;
        }
        ls.ajax(aRouter.galleryajax + 'markfriend', {
            'idImage': idImage,
            'login': login,
            'selection': selection
        },  function (response) {
            if (response.bStateError) {
                ls.msg.error(response.sMsgTitle, response.sMsg);
            } else {
                var li = '<li id="target-' + response.idUser + '" class="selected-new">'
                    + '<a class="user" href="' + response.sPath + '">' + login + '</a>'
                    + '<a href="#" class="remove" onclick="ls.gallery.removeMark('
                    + idImage + ', ' + response.idUser + ', this); return false;"></a>'
                    + '</li>',
                    div = '<div class="image-marker" id="marked-user-' + response.idUser + '" style="top: ' + selection.y1 + 'px; left: '
                    + selection.x1 + 'px; width: ' + selection.width + 'px; height: ' + selection.height + 'px;">'
                    + '<div class="marker-wrap" style="width: ' + selection.width + 'px; height: ' + selection.height + 'px;  display: none;">'
                    + '<div class="marker-inside" style="width: ' + (selection.width - 2) + '}px; height: ' + (selection.height - 2) + 'px"></div>'
                    + '<div class="user-href-wrap"><a class="user" href="' + response.sPath + '">' + login + '</a></div>'
                    + '</div></div>';
                jQuery('#selected-people').append(li);
                jQuery('#image').append(div);
                hideMarkFriend();
                ls.msg.notice(response.sMsgTitle, response.sMsg);
            }
        });
    });
    // cancel mark
    jQuery('#image-mark-ready').live('click', function (event) {
        event.preventDefault();
        cancelMarkFriend();
    });

    // positioning select mark
    jQuery('#image img.select-pic').live('click', function (event) {
        event.preventDefault();
        clickSelect(event);
    });
    // positioning select mark
    jQuery('.imgareaselect-outer').live('click', function (event) {
        event.preventDefault();
        clickSelect(event);
    });
    // next|prev img on img click
    jQuery('#image img.gallery-big-photo:not(.select-pic)').live('click', function (event) {
        if (jQuery(this).parent('a').length) {
            return;
        }
        event.preventDefault();
        var offset = jQuery('#image img.gallery-big-photo').offset(),
            width = jQuery('#image img.gallery-big-photo').width();
        if (jQuery('a.gal-left').length && (offset.left + width * 0.4) > event.pageX) {
            jQuery('a.gal-left').click();
        }
        if (jQuery('a.gal-right').length && (offset.left + width * 0.6) < event.pageX) {
            jQuery('a.gal-right').click();
        }
    });

    jQuery('#image img.gallery-big-photo:not(.select-pic)').live('mousemove', function (event) {
        event.preventDefault();
        var offset = jQuery('#image img.gallery-big-photo').offset(),
            width = jQuery('#image img.gallery-big-photo').width();
        if (jQuery('a.gal-left').length && (offset.left + width * 0.4) > event.pageX ) {
            jQuery(this).css('cursor', 'pointer');
        } else if (jQuery('a.gal-right').length && (offset.left + width * 0.6) < event.pageX ) {
            jQuery(this).css('cursor', 'pointer');
        } else {
            jQuery(this).css('cursor', 'auto');
        }
    });
    function imageLoaded(data) {
        jQuery('#view-image').html(data.sImageContent);
        jQuery('#image-comments').html(data.sCommentContent);
        if (jQuery('#select-friends').length) {
            initMark();
        }
    }
    // add ajax load image
    if (jQuery('.gallery-navigation').length) {
        // handle keypress
        jQuery(document).keydown(function(event){
            if ((event.keyCode || event.which) == 37 && event.ctrlKey) {
                event.preventDefault();
                jQuery('a.gal-left').click();
            } else if ((event.keyCode || event.which) == 39 && event.ctrlKey) {
                event.preventDefault();
                jQuery('a.gal-right').click();
                return false;
            }
        });

        var History = window.History; // Note: We are using a capital H instead of a lower h
        if (!History.enabled) {
            // History.js is disabled for this browser.
            // This is because we can optionally choose to support HTML4 browsers or not.
            return false;
        }

        var title = document.title,
            rootUrl = History.getRootUrl();


        jQuery('a.ajaxy').live('click', function (event) {
            if (ias) {
                cancelMarkFriend();
                jQuery(".imgareaselect-selection").parent().remove();
                jQuery(".imgareaselect-outer").remove();
                ias = null;
            }
            var
                $this = jQuery(this),
                url = $this.attr('href'),
                title = $this.attr('title') || null;

            // Continue as normal for cmd clicks etc
            if (event.which == 2 || event.metaKey) {
                return true;
            }

            // Ajaxify this link
            History.pushState(null, title, url);
            event.preventDefault();
            return false;
        });

        History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
            var State = History.getState(),
                url = State.url,
                relativeUrl = url.replace(rootUrl,''),
                match = url.match(/image\/(\d+)/i);
            // search image id
            if (!match || !match[1]) {
                document.location.href = url;
                return false;
            }

            var params = {
                id : match[1],
                security_ls_key: LIVESTREET_SECURITY_KEY
            };
            // load blocks
            jQuery.ajax({
				url: aRouter.galleryajax + 'getimage',
                type: 'POST',
                data: params,
				success: function(data, textStatus, jqXHR) {
                    if (data.bStateError || !data.sImageContent || !data.sCommentContent) {
                        document.location.href = url;
                        return false;
                    }
                    // preload image
                    if (data.sImageUrl) {
                        var galleryImage = new Image();
                        galleryImage.onload = function () {
                            imageLoaded(data);
                        };
                        galleryImage.src = data.sImageUrl;
                    } else {
                        imageLoaded(data);
                    }

                    document.title = title;

					// Inform Google Analytics of the change
					if (typeof window.pageTracker !== 'undefined') {
						window.pageTracker._trackPageview(relativeUrl);
					}

					// Inform ReInvigorate of a state change
					if (typeof window.reinvigorate !== 'undefined' && typeof window.reinvigorate.ajax_track !== 'undefined') {
						reinvigorate.ajax_track(url);
						// ^ we use the full url here as that is what reinvigorate supports
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					document.location.href = url;
					return false;
				}
			}); // end ajax
        });
    }
});