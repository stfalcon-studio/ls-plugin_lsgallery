{assign var="sidebarPosition" value='left'}
{include file='header.tpl' menu='people'}

{include file='actions/ActionProfile/profile_top.tpl'}

<h2 class="header-table">{$aLang.plugin.lsgallery.lsgallery_image_user_marked}{$iPhotoCount} {$iPhotoCount|declension:$aLang.plugin.lsgallery.lsgallery_declension_images}</h2>

<div class="content">
	{include file="`$sTemplatePathLsgallery`photo_list.tpl" aImage=$aImage bSlideshow=false}
</div>

{include file='paging.tpl' aPaging="$aPaging"}

{include file='footer.tpl'}