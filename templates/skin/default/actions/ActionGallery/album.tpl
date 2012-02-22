{include file='header.tpl' menu="album"}
<div class="topic gallery-topic">
    <h1 class="title">
        {if count($aImages)}
            <a id="gallery-slideshow" href="#">{$aLang.lsgallery_album_slideshow}</a>
        {/if}
        {$oAlbum->getTitle()|escape:'html'}
    </h1>
    {if $oUserCurrent && ($oUserCurrent->isAdministrator() || $oUserCurrent->getId() == $oAlbum->getUserId()) && count($aImages)}
    <div class="info-top">    
        <ul class="actions">
                <li>
                    <a class="add-images" href="{$oAlbum->getUrlFull('images')}" >{$aLang.lsgallery_album_add_image}</a>
                </li>
        </ul>
    </div>            
    {/if}
    <div class="content">
        {if count($aImages)}
            {include file='photo_list.tpl' aImages=$aImages bSlideshow=true}
        {else}    
			<div class="centered">
				<a id="album-add-images" class="add-images" href="{$oAlbum->getUrlFull('images')}">{$aLang.lsgallery_album_add_image}</a>
			</div>
        {/if}    
    </div>
    
</div>
{include file='paging.tpl' aPaging="$aPaging"}

{include file='footer.tpl'}