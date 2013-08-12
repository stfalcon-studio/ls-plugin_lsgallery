<section class="block gallery-block block-type-blog" id="block_album">
    <header class="block-header">
        <h3>
            {$aLang.plugin.lsgallery.lsgallery_albums_about}
        </h3>
    </header>
    {if $oUserCurrent && ($oUserCurrent->isAdministrator() || $oUserCurrent->getId() == $oAlbum->getUserId())}
    <div class="info-top block-content">
        <ul class="actions">
            <li class="edit">
                <i class="icon-synio-actions-edit"></i>
                <a href="{$oAlbum->getUrlFull('edit')}" title="{$aLang.blog_edit}" class="edit">{$aLang.plugin.lsgallery.lsgallery_update_album_title}</a>
            </li>
            <li class="delete">
                <i class="icon-synio-actions-delete"></i>
                <a class="delete" href="{$oAlbum->getUrlFull('delete')}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" title="{$aLang.blog_delete}" onclick="return confirm('{$aLang.plugin.lsgallery.lsgallery_album_delete_confirm}');" >{$aLang.plugin.lsgallery.lsgallery_delete_album_title}</a>
            </li>
        </ul>
    </div>
    {/if}
	<div class="block-content" id="block_album_content">
		{assign var="oImage" value=$oAlbum->getCover()}
        {assign var="oUser" value=$oAlbum->getUser()}
            <a class="gallery-item album-cover" href="{$oAlbum->getUrlFull()}">
                {if $oImage}
                    <img class="image-48" src="{$oImage->getWebPath('100crop')}" alt="{$oAlbum->getTitle()|escape:'html'}" />
                {else}
                    <div class="empty-album"></div>
                {/if}
            </a>
            {$oAlbum->getDescription()|strip_tags}
	</div>
    <div class="gallery-user block-content">
        <a class="author" href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
        <time datetime="{date_format date=$oAlbum->getDateAdd() format='c'}" title="{date_format date=$oAlbum->getDateAdd() format='j F Y, H:i'}">
	    {date_format date=$oAlbum->getDateAdd() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}
        </time>
    </div>
	<footer>
        {hook run='block_album_footer_begin' oAlbum=$oAlbum}
		<a href="{router page='my'}{$oUser->getLogin()}/album">{$aLang.plugin.lsgallery.lsgallery_albums_user_all}</a>
	</footer>
</section>
