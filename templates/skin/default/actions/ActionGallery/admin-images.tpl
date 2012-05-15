{include file='header.tpl' menu="album"}

{include file='menu.album_edit.tpl'}

{assign var=oImages value=$oAlbumEdit->getImages()}

<script type="text/javascript">
var DIR_WEB_LSGALLERY_SKIN = '{$sTemplateWebPathLsgallery}';
if (jQuery.browser.flash) {
	ls.gallery.initSwfUpload({
		post_params: { 'album_id':'{$oAlbumEdit->getId()}' }
	});
}
</script>

<div id="album-images-admin" class="topic-photo-upload">
    <div class="topic-photo-upload-rules">
        <a href="#" id="images-start-upload">{$aLang.lsgallery_images_upload_choose}</a>
        <p class="left">{$aLang.lsgallery_images_upload_rules|ls_lang:"SIZE%%`$oConfig->get('plugin.lsgallery.image_max_size')`":"COUNT%%`$oConfig->get('plugin.lsgallery.count_image_max')`"}</p>
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
                    <label class="description">{$aLang.lsgallery_image_description}</label><br/>
                    <textarea onBlur="ls.gallery.setImageDescription({$oImage->getId()}, this.value)">{$oImage->getDescription()}</textarea><br />
                    <label class="tags">{$aLang.lsgallery_image_tags}</label><br/>
                    <input type="text" class="autocomplete-image-tags" onBlur="ls.gallery.setImageTags({$oImage->getId()}, this.value)" value="{$oImage->getImageTags()}"/><br/>
                    <div class="options-line">
                        <span class="photo-preview-state">
                            <span id="image_preview_state_{$oImage->getId()}">
                            {if $bIsMainImage}
                                {$aLang.lsgallery_album_image_cover}
                            {else}
                                <a href="javascript:ls.gallery.setPreview({$oImage->getId()})" class="mark-as-preview">{$aLang.lsgallery_album_set_image_cover}</a>
                            {/if}
                            </span>
                            <br/>
                            <a href="javascript:ls.gallery.toggleForbidComment({$oImage->getId()})" class="image-comment">{if $oImage->getForbidComment()}{$aLang.lsgallery_unset_forbid_comments}{else}{$aLang.lsgallery_set_forbid_comments}{/if}</a>
                        </span>

                        <a href="javascript:ls.gallery.deleteImage({$oImage->getId()})" class="image-delete">{$aLang.lsgallery_album_image_delete}</a>
                    </div>
                </li>
                {assign var=bIsMainImage value=false}
            {/foreach}
        {/if}
    </ul>
</div>
{include file='paging.tpl' aPaging="$aPaging"}

{include file='footer.tpl'}