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
                + '<label class="description">' + ls.lang.get('plugin.lsgallery.lsgallery_image_description') + '</label><br/>'
                + '<textarea onBlur="ls.gallery.setImageDescription(' + response.id + ', this.value)"></textarea><br />'
                + '<label class="tags">' + ls.lang.get('plugin.lsgallery.lsgallery_image_tags') + '</label><br/>'
                + '<input type="text" class="autocomplete-image-tags" onBlur="ls.gallery.setImageTags(' + response.id + ', this.value)"/><br/>'
                + '<div class="options-line"><span class="photo-preview-state"><span id="image_preview_state_' + response.id + '">'
                + '<a href="javascript:ls.gallery.setPreview(' + response.id + ')" class="mark-as-preview">' + ls.lang.get('plugin.lsgallery.lsgallery_album_set_image_cover') + '</a></span><br/>'
                + '<a href="#" class="image-move">' + ls.lang.get('plugin.lsgallery.lsgallery_image_move_album') + '</a></span>'
                + '<a href="javascript:ls.gallery.deleteImage(' + response.id + ')" class="image-delete">' + ls.lang.get('plugin.lsgallery.lsgallery_album_image_delete') + '</a>'
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
        if (!confirm(ls.lang.get('plugin.lsgallery.lsgallery_album_image_delete_confirm'))) {
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
                    $('#image_preview_state_' + tmpId).html('<a href="javascript:ls.gallery.setPreview(' + tmpId + ')" class="mark-as-preview">' + ls.lang.get('plugin.lsgallery.lsgallery_album_set_image_cover') + '</a>');
                });
                $('#image_' + id).addClass('marked-as-preview');
                $('#image_preview_state_' + id).html(ls.lang.get('plugin.lsgallery.lsgallery_album_image_cover'));
            } else {
                ls.msg.error(response.sMsgTitle, response.sMsg);
            }
        });

    };

    // set image descr
    this.setImageDescription = function (id, text) {
        jQuery('#image_' + id + ' label.description').html(ls.lang.get('plugin.lsgallery.lsgallery_image_description')).removeClass('gallery-loader-success').addClass('gallery-loader');;
        ls.ajax(aRouter.galleryajax + 'setimagedescription', {
            'id': id,
            'text': text
        },  function (result) {
            if (result.bStateError) {
                ls.msg.error('Error', 'Please try again later');
                jQuery('#image_' + id + 'label.description').removeClass('gallery-loader');
            } else {
                jQuery('#image_' + id + ' label.description').html(ls.lang.get('plugin.lsgallery.lsgallery_image_description_updated')).removeClass('gallery-loader').addClass('gallery-loader-success');
            }
        }, {
            error : function () {
                jQuery('#image_' + id + 'label.description').removeClass('gallery-loader');
            }
        });
    };
    // set image tags
    this.setImageTags = function (id, text) {
        if (jQuery('ul.ui-autocomplete').css('display') === 'block') {
            return;
        }

        jQuery('#image_' + id + ' label.tags').html(ls.lang.get('plugin.lsgallery.lsgallery_image_tags')).removeClass('gallery-loader-success').addClass('gallery-loader');
        ls.ajax(aRouter.galleryajax + 'setimagetags', {
            'id': id,
            'tags': text
            },  function (result) {
                if (result.bStateError) {
                    ls.msg.error('Error', 'Please try again later');
                    jQuery('#image_' + id + 'label.tags').removeClass('gallery-loader');
                } else {
                    jQuery('#image_' + id + ' label.tags').html(ls.lang.get('plugin.lsgallery.lsgallery_image_tags_updated')).removeClass('gallery-loader').addClass('gallery-loader-success');
                }
            }, {
                error : function () {
                    jQuery('#image_' + id + 'label.tags').removeClass('gallery-loader');
                }
            }
        );
    };

    this.moveImage = function () {
        ls.ajax(aRouter.galleryajax + 'moveimage', {
            idImage: jQuery('#image_move_id').val(),
            idAlbum: jQuery('#album_to_id').val()
        },  function (response) {
            if (response.bStateError) {
                ls.msg.error(response.sMsgTitle, response.sMsg);
            } else {
                var id = jQuery('#image_move_id').val();
                jQuery('#image_' + id).remove();
                ls.msg.notice(response.sMsgTitle, response.sMsg);
            }
            jQuery('#image_move_id').val('');
            jQuery('#album_to_id').val('');
            jQuery('#move_image_form').jqmHide();
        });
    };

    if (!this.initImageUpload) {
        this.initImageUpload = function(album_id) {
            if (jQuery.browser.flash) {
                ls.gallery.initSwfUpload({
                    post_params: { 'album_id': album_id }
                });
            } else {
                alert(ls.lang.get('plugin.lsgallery.lsgallery_flash_upload_init_error'));
            }
        };
    }

    return this;
}).call(ls.gallery || {}, jQuery);

jQuery('document').ready(function(){
    jQuery('.js-infobox-vote-image').poshytip({
        content: function() {
            var id = $(this).attr('id').replace('vote_area_image_','vote-info-topic-');
            return $('#'+id).html();
        },
        className: 'infobox-topic',
        alignTo: 'target',
        alignX: 'center',
        alignY: 'top',
        offsetX: 2,
        offsetY: 5,
        liveEvents: true,
        showTimeout: 100
    });

    // перемещаем изображение в другой альбом
    if(jQuery('#move_image_form')[0]) {
        jQuery('#move_image_form').jqm();
    }
    jQuery(document).on('click', '.image-move', function(event){
        event.preventDefault();
        if (!jQuery('#move_image_form').length) {
            jQuery(this).remove();
            return;
        }
        var id = jQuery(this).parents('li').attr('id').replace('image_', '');
        jQuery('#image_move_id').val(id);
        jQuery('#move_image_form').jqmShow();
    });

    // autocomplete for image tags
    ls.autocomplete.add(jQuery(".autocomplete-image-tags"), aRouter.galleryajax + 'autocompleteimagetag/', true);
    ls.autocomplete.add(jQuery(".autocomplete-image-tags-single"), aRouter.galleryajax + 'autocompleteimagetag/', false);

    // init fancybox for gallery
    if (jQuery('a.gal-expend').length) {
        jQuery('a.gal-expend').fancybox();
    };

    // change random images
    jQuery(document).on('click', '#gallery-reload', function (event) {
        event.preventDefault();
        ls.ajax(aRouter.galleryajax + 'getrandomimages', {},
            function (result) {
                if (result.sHtml) {
                    jQuery('#block-random-images').html(result.sHtml);
                }
            });
    });
    // Поиск по тегам
    jQuery(document).on('submit', '#tag__image_search_form', function (event) {
        event.preventDefault();
        window.location = aRouter.gallery + 'tag/' + jQuery('#tag_search').val() + '/';
    });

    // show slideshow
    jQuery(document).on('click', '#gallery-slideshow', function (event) {
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

    // next|prev img on img click
    jQuery(document).on('click', '#image img.gallery-big-photo:not(.select-pic)',function (event) {
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

    jQuery(document).on('mousemove', '#image img.gallery-big-photo:not(.select-pic)', function (event) {
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

    jQuery(document).on('keypress', '#album_description', function(){imposeMaxLength(this, 512)});
});

function imposeMaxLength(Object, MaxLen) {
    if (Object.value.length > MaxLen) {
        Object.value = Object.value.substring(0, MaxLen);
    }
}
