<div class="block gallery-block" id="block_album">
	<h2>{$aLang.lsgallery_albums_about}</h2>
    {if $oUserCurrent && ($oUserCurrent->isAdministrator() || $oUserCurrent->getId() == $oAlbum->getUserId())}
    <div class="info-top">    
        <ul class="actions">
                <li>
                    <a href="{$oAlbum->getUrlFull('edit')}" title="{$aLang.blog_edit}" class="edit">{$aLang.lsgallery_update_album_title}</a>
                </li>
                <li>
                    <a class="delete" href="{$oAlbum->getUrlFull('delete')}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" title="{$aLang.blog_delete}" onclick="return confirm('{$aLang.lsgallery_album_delete_confirm}');" >{$aLang.lsgallery_delete_album_title}</a>
                </li>
        </ul>
    </div>            
    {/if}
	<div class="block-content" id="block_album_content">
		{assign var="oImage" value=$oAlbum->getCover()}
        {assign var="oUser" value=$oAlbum->getUser()}
            <a class="gallery-item" href="{$oAlbum->getUrlFull()}">
                {if $oImage}
                    <img class="100-image" src="{$oImage->getWebPath('100crop')}" alt="{$oAlbum->getTitle()|escape:'html'}" />
                {else}
                    <div class="empty-album"></div>
                {/if} 
            </a>
            {$oAlbum->getDescription()|strip_tags}
	</div>
    <div class="gallery-user">
        <a class="user" href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
        {date_format date=$oAlbum->getDateAdd()}
    </div>
	<div class="bottom">
		<a href="{router page='gallery'}user/{$oUser->getLogin()}">{$aLang.lsgallery_albums_user_all}</a>
	</div>
</div>
