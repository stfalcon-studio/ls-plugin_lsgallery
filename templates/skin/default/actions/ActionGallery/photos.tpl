{include file='header.tpl' menu="album"}
<div class="topic gallery-topic">
    {if $sMenuSubItemSelect=='new'}
        <h1 class="title">{$aLang.lsgallery_photo_new_title}</h1>
    {else if $sMenuSubItemSelect=='best'}
        <h1 class="title">{$aLang.lsgallery_photo_best_title}</h1>
    {else}
        <h1 class="title">{$aLang.lsgallery_image_user_marked}{$iPhotoCount} {$iPhotoCount|declension:$aLang.lsgallery_declension_images}</h1>
    {/if}
    <div class="content">
        {include file="`$sTemplatePathLsgallery`photo_list.tpl" aImage=$aImage bSlideshow=false}
    </div>
</div>


{include file='paging.tpl' aPaging="$aPaging"}

{include file='footer.tpl'}