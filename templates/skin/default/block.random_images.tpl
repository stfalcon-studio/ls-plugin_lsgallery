{if (count($aRandomImages))}
<div class="gallery-albums-preview">
    <h2 class="page-header">{$aLang.plugin.lsgallery.lsgallery_iamge_rand}</h2>
    <ul id="block-random-images">
        {foreach from=$aRandomImages item=oImage}
            <li><a href="{$oImage->getUrlFull()}"><img class="image-100" src="{$oImage->getWebPath('100crop')}" alt="Image" /></a></li>
        {/foreach}
    </ul>
    <div class="gallery-albums-right">
        <a id="gallery-reload" href="#"></a>
    </div>
</div>
{/if}
