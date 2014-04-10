{include file='header.tpl' menu="album"}
<div class="topic gallery-topic">
    <h2 class="page-header">
        {if count($aImages)}
                <a class="gallery-image-sort"
                   href="{$oAlbum->getUrlFull()}?order={$sOrderLink}">{$aLang.plugin.lsgallery["lsgallery_image_sort_{$sOrderLink}"]}</a>
            <a id="gallery-slideshow" href="#">{$aLang.plugin.lsgallery.lsgallery_album_slideshow}</a>
        {/if}
        {$oAlbum->getTitle()|escape:'html'}
    </h2>
    {if $LS->ACL_AllowAdminAlbumImages($oUserCurrent, $oAlbum)}
    <div class="info-top">
        <ul class="actions">
            <li>
                <a class="add-images" href="{$oAlbum->getUrlFull('images')}" >{$aLang.plugin.lsgallery.lsgallery_album_add_image}</a>
            </li>
        </ul>
    </div>
    {/if}
    <div class="content">
        {if count($aImages)}
            {include file="`$sTemplatePathLsgallery`photo_list.tpl" aImages=$aImages bSlideshow=true sOrder=$sOrder}
        {elseif $oUserCurrent && ($oUserCurrent->isAdministrator() || $oUserCurrent->getId() == $oAlbum->getUserId())}
            <div class="centered">
                <a id="album-add-images" class="add-images" href="{$oAlbum->getUrlFull('images')}">{$aLang.plugin.lsgallery.lsgallery_album_add_image}</a>
            </div>
        {/if}
    </div>

</div>
{include file='paging.tpl' aPaging="$aPaging"}

{include file='footer.tpl'}