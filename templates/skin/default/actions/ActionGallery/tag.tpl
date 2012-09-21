{include file='header.tpl' menu="album"}

<form method="GET" class="tags-search" id="tag__image_search_form">
	<input type="text" name="tag" value="{$sTag|escape:'html'}" class="input-text input-width-full autocomplete-image-tags-single" id="tag_search" >
</form>

{include file="`$sTemplatePathLsgallery`photo_list.tpl" aImages=$aImage bSlideshow=false}

{include file='paging.tpl' aPaging="$aPaging"}

{include file='footer.tpl'}