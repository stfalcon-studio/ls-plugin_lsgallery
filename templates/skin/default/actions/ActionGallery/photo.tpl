{include file='header.tpl' menu="album"}
<div class="topic gallery-topic">
    <h1 class="title">{$aLang.lsgallery_photo_day}</h1>
    {if $oImage}
        {include file='photo_view.tpl' oImage=$oImage bSelectFriends=false bSliderImage=false}
    {/if}
</div>
    
{include file='block.albums_list.tpl' aAlbums=$aAlbums}

{include file='block.random_images.tpl' aRandomImages=$aRandomImages}



{include file='footer.tpl'}