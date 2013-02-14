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
    ls.blocks.options.type.block_gallery_new_images = {
        url: aRouter.galleryajax + 'getnewimages/'
    };
    ls.blocks.options.type.block_gallery_best_images = {
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
    }

    return this;
}).call(ls.gallery || {}, jQuery);

var ias = null;

jQuery('document').ready(function(){
    $('.js-infobox-vote-image').poshytip({
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
    jQuery('#move_image_form').jqm();
    jQuery('.image-move').live('click', function(event){
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
    jQuery('#gallery-reload').live('click', function (event) {
        event.preventDefault();
        ls.ajax(aRouter.galleryajax + 'getrandomimages', {},
            function (result) {
                if (result.sHtml) {
                    jQuery('#block-random-images').html(result.sHtml);
                }
            });
    });
    ls.blocks.init('block_gallery', {group_items: true});
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
//        if (jQuery('#select-friends').length) {
//            initMark();
//        }
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

function imposeMaxLength(Object, MaxLen) {
    if (Object.value.length > MaxLen) {
        Object.value = Object.value.substring(0, MaxLen);
    }
}