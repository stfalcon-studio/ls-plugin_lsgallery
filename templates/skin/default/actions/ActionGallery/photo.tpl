{include file='header.tpl' menu="album"}
<div class="topic gallery-topic">
    <h1 class="title">{$aLang.lsgallery_photo_day}</h1>
    {if $oImage}
        {include file="`$sTemplatePathLsgallery`photo_view.tpl" oImage=$oImage bSelectFriends=false bSliderImage=false}
    {/if}
</div>

{include file="`$sTemplatePathLsgallery`block.albums_list.tpl" aAlbums=$aAlbums}

{include file="`$sTemplatePathLsgallery`block.random_images.tpl" aRandomImages=$aRandomImages}



{include file='footer.tpl'}