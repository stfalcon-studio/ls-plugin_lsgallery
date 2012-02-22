<h2>{$aLang.lsgallery_admin_album_title}: <a href="{$oAlbumEdit->getUrlFull()}">{$oAlbumEdit->getTitle()}</a></h2>

<ul class="switcher">
	<li {if $sMenuSubItemSelect == 'update'}class="active"{/if}><a href="{$oAlbumEdit->getUrlFull('edit')}">{$aLang.lsgallery_update_album_title}</a></li>
	<li {if $sMenuSubItemSelect == 'admin-images'}class="active"{/if}><a href="{$oAlbumEdit->getUrlFull('images')}">{$aLang.lsgallery_images_album_title}</a></li>
	<li><a href="{$oAlbumEdit->getUrlFull('delete')}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" title="{$aLang.blog_delete}" onclick="return confirm('{$aLang.lsgallery_album_delete_confirm}');" >{$aLang.lsgallery_delete_album_title}</a></li>
</ul>