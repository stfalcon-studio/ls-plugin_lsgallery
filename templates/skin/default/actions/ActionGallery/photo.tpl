{include file='header.tpl' menu="album" menu_content='album'}
<div class="topic gallery-topic">
    <h2 class="page-header">{$aLang.plugin.lsgallery.lsgallery_photo_day}</h2>
    {if $oImage}
        {include file="`$sTemplatePathLsgallery`photo_view.tpl" oImage=$oImage bSliderImage=false sOrder=$sOrder}
    {/if}
</div>

{include file="`$sTemplatePathLsgallery`block.albums_list.tpl" aAlbums=$aAlbums}

{include file="`$sTemplatePathLsgallery`block.random_images.tpl" aRandomImages=$aRandomImages}



{include file='footer.tpl'}