{if $aProfileImages}
<div class="gallery-albums-preview">
    <h2 class="page-header">{$aLang.plugin.lsgallery.lsgallery_image_user_marked}{$iPhotoCount} {$iPhotoCount|declension:$aLang.plugin.lsgallery.lsgallery_declension_images}</h2>
    <ul id="block-random-images">
        {foreach from=$aProfileImages item=oImage}
            <li><a href="{$oImage->getUrlFull()}"><img class="image-100" src="{$oImage->getWebPath('100crop')}" alt="Image" /></a></li>
        {/foreach}
    </ul>
    <div class="gallery-albums-right">
        <a class="gallery-next" href="{router page='gallery'}usermarked/{$oUserProfile->getLogin()}">{$aLang.plugin.lsgallery.lsgallery_albums_show_all}</a>
    </div>
</div>
{/if}