{include file='header.tpl' menu="album" menu_content='album'}
<div class="topic gallery-topic">
    {if $sMenuSubItemSelect=='new'}
        <h2 class="page-header">{$aLang.plugin.lsgallery.lsgallery_photo_new_title}</h2>
    {elseif $sMenuSubItemSelect=='best'}
        <h2 class="page-header">{$aLang.plugin.lsgallery.lsgallery_photo_best_title}</h2>
    {else}
        <h2 class="page-header">{$aLang.plugin.lsgallery.lsgallery_image_user_marked}{$iPhotoCount} {$iPhotoCount|declension:$aLang.plugin.lsgallery.lsgallery_declension_images}</h2>
    {/if}
    <div class="content">
        {include file="`$sTemplatePathLsgallery`photo_list.tpl" aImage=$aImage bSlideshow=false}
    </div>
</div>


{include file='paging.tpl' aPaging="$aPaging"}

{include file='footer.tpl'}