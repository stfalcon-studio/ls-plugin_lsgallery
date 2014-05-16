<div class="gallery-photos-list">
    <ul id="album-images">
        {if $bSlideshow}
            {foreach from=$aImages item=oImage}
                <a class="image-slideshow" rel="slidegroup" href="{$oImage->getWebPath()}"></a>
            {/foreach}
        {/if}
        {foreach from=$aImages item=oImage}
            <li>
                <a href="{$oImage->getUrlFull()}?order={$sOrder}"><img class="image-100" src="{$oImage->getWebPath('100crop')}" alt="Image" /></a>
            </li>
        {/foreach}
    </ul>
</div>
