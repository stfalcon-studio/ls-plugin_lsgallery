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
    ls.blocks.options.type.block_gallery_images_comments = {
        url: aRouter.galleryajax + 'getimagescomments/'
    };
}

jQuery('document').ready(function(){
    ls.blocks.init('block_gallery', {group_items: true});
});

