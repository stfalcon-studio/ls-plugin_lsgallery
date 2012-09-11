<section class="block gallery-block block-type-block_gallery" id="block_gallery">
    <header class="block-header sep">
        <h3>
            {$aLang.plugin.lsgallery.lsgallery_photo}
        </h3>
        <ul class="nav nav-pills js-block_gallery-nav">
            <li id="block_gallery_item_new" class="active js-block-block_gallery-item" data-type="new_images">{$aLang.plugin.lsgallery.lsgallery_photo_new}</li>
            <li id="block_gallery_item_best" class="js-block-block_gallery-item" data-type="best_images">{$aLang.plugin.lsgallery.lsgallery_photo_best}</li>
        </ul>
    </header>


	<div class="block-content js-block-block_gallery-content" id="block_gallery_content">
		{$sStreamImages}
	</div>
</section>

