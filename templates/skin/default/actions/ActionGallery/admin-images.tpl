{include file='header.tpl' menu="album" menu_content="album_edit"}

<h2 class="page-header">{$aLang.plugin.lsgallery.lsgallery_admin_album_title}: <a href="{$oAlbumEdit->getUrlFull()}">{$oAlbumEdit->getTitle()}</a></h2>

{assign var=oImages value=$oAlbumEdit->getImages()}

<script type="text/javascript">
var DIR_WEB_LSGALLERY_SKIN = '{$sTemplateWebPathLsgallery}';
jQuery(document).on('ready', function(){
    setTimeout(function(){
        ls.gallery.initImageUpload('{$oAlbumEdit->getId()}');
    }, 200);
});
</script>

<div id="album-images-admin" class="topic-photo-upload">
    {hook run='image_upload_begin' oImages=$oImages oAlbum=$oAlbum}
    <div class="topic-photo-upload-rules">
        <a href="#" id="images-start-upload">{$aLang.plugin.lsgallery.lsgallery_images_upload_choose}</a>
        <p class="left note">{$aLang.plugin.lsgallery.lsgallery_images_upload_rules|ls_lang:"SIZE%%`$oConfig->get('plugin.lsgallery.image_max_size')`":"COUNT%%`$oConfig->get('plugin.lsgallery.count_image_max')`"}</p>
    </div>
    <div class="clear"></div>
	<ul id="swfu_images">
        {if count($aImages)}
            {foreach from=$aImages item=oImage}
                {if $oAlbumEdit->getCoverId() == $oImage->getId()}
                    {assign var=bIsMainImage value=true}
                {/if}
                <li id="image_{$oImage->getId()}" {if $bIsMainImage}class="marked-as-preview"{/if}>
                    <img class="image-100" src="{$oImage->getWebPath('100crop')}" alt="image" />
                    <label class="description">{$aLang.plugin.lsgallery.lsgallery_image_description}</label><br/>
                    <textarea class="input-text input-width-full" onBlur="ls.gallery.setImageDescription({$oImage->getId()}, this.value)">{$oImage->getDescription()}</textarea><br />
                    <label class="tags">{$aLang.plugin.lsgallery.lsgallery_image_tags}</label><br/>
                    <input type="text" class="autocomplete-image-tags input-text input-width-full" onBlur="ls.gallery.setImageTags({$oImage->getId()}, this.value)" value="{$oImage->getImageTags()}"/><br/>
                    <div class="options-line">
                        <span class="photo-preview-state">
                            <span id="image_preview_state_{$oImage->getId()}">
                            {if $bIsMainImage}
                                {$aLang.plugin.lsgallery.lsgallery_album_image_cover}
                            {else}
                                <a href="javascript:ls.gallery.setPreview({$oImage->getId()})" class="mark-as-preview">{$aLang.plugin.lsgallery.lsgallery_album_set_image_cover}</a>
                            {/if}
                        </span>
                            <br/>
                            <a href="#" class="image-move">{$aLang.plugin.lsgallery.lsgallery_image_move_album}</a>
                        </span>

                        <a href="javascript:ls.gallery.deleteImage({$oImage->getId()})" class="image-delete">{$aLang.plugin.lsgallery.lsgallery_album_image_delete}</a>
                    </div>
                </li>
                {assign var=bIsMainImage value=false}
            {/foreach}
        {/if}
    </ul>
    {hook run='image_upload_end' oImages=$oImages oAlbum=$oAlbum}
</div>
{if count($aAlbums)}
    <div class="move-image-form modal" id="move_image_form">
        <header class="modal-header">
            <h3>{$aLang.plugin.lsgallery.lsgallery_image_select_album}</h3>
            <a href="#" class="close jqmClose"></a>
        </header>
	    <div class="modal-content">
            <form action="" method="POST">
	            <p>
	                <select id="album_to_id" name="album_to_id" class="input-width-full">
		            {foreach from=$aAlbums item=oAlbum}
	                    <option value="{$oAlbum->getId()}">{$oAlbum->getTitle()}</option>
		            {/foreach}
	                </select>
	            </p>

                <input type="hidden" value="" name="image_move_id" id="image_move_id"/>
                <input type="button" value="{$aLang.plugin.lsgallery.lsgallery_image_move}" class="button button-primary" onclick="ls.gallery.moveImage();" />
                <input type="button" value="{$aLang.plugin.lsgallery.lsgallery_cancel}" class="button jqmClose" />
            </form>
	    </div>

    </div>
{/if}
{include file='paging.tpl' aPaging="$aPaging"}

{include file='footer.tpl'}