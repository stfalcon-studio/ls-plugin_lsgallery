{if count($aAlbums)}
<div class="gallery-albums-preview">
    <h2 class="page-header">{$aLang.plugin.lsgallery.lsgallery_albums}</h2>
    <ul id="block-albums-list">
        {foreach from=$aAlbums item=oAlbum}
            {assign var="oImage" value=$oAlbum->getCover()}
            <li>
                <div class="bk">
                    <a href="{$oAlbum->getUrlFull()}">
                        {if $oImage}
                        <img class="image-100" src="{$oImage->getWebPath('100crop')}" alt="{$oAlbum->getTitle()|escape:'html'}" />
                        {else}
                            <div class="empty-album"></div>
                        {/if}
                    </a>
                </div>
            </li>
        {/foreach}
        <div class="gallery-albums-right">
            {if $oUserProfile}
                <a class="gallery-next" href="{router page='my'}{$oUserProfile->getLogin()}/album/">{$aLang.plugin.lsgallery.lsgallery_albums_user_all}</a>
            {else}
                <a class="gallery-next" href="{router page='gallery'}albums/">{$aLang.plugin.lsgallery.lsgallery_albums_show_all}</a>
            {/if}
        </div>
    </ul>
</div>
{/if}
