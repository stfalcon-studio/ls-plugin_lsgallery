<section class="block gallery-block block-type-block_gallery" id="block_gallery">
    {hook run='block_stream_gallery_nav_item' assign="sItemsHook"}
    <header class="block-header sep">
        <h3>
            {$aLang.plugin.lsgallery.lsgallery_photo}
        </h3>
        <ul class="nav nav-pills js-block_gallery-nav">
            <li id="block_gallery_item_new" class="js-block-block_gallery-item" data-type="new_images"><a href="#">{$aLang.plugin.lsgallery.lsgallery_photo_new}</a></li>
            <li id="block_gallery_item_best" class="active js-block-block_gallery-item" data-type="best_images"><a href="#">{$aLang.plugin.lsgallery.lsgallery_photo_best}</a></li>
            {$sItemsHook}
        </ul>
    </header>


	<div class="block-content js-block-block_gallery-content" id="block_gallery_content">
		{$sBestImages}
	</div>
</section>
