{include file='header.tpl' menu="album"}

<form method="GET" class="tags-search" id="tag__image_search_form">
	<img src="{cfg name='path.static.skin'}/images/tagcloud.gif" class="tagcloud" alt="" />&nbsp;
	<input type="text" name="tag" value="{$sTag|escape:'html'}" class="tags-input" id="tag_search" >
</form>

{include file="`$sTemplatePathLsgallery`photo_list.tpl" aImages=$aImage bSlideshow=false}

{include file='paging.tpl' aPaging="$aPaging"}

{include file='footer.tpl'}