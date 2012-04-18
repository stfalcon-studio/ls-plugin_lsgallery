{include file='header.tpl' menu="album"}
<div class="topic gallery-topic">
<h1 class="title">{$aLang.lsgallery_albums}</h1>
    <div class="content">
        <div class="gallery-albums-list">
            <ul id="albums">
                {foreach from=$aAlbums item=oAlbum}
                    {assign var="oImage" value=$oAlbum->getCover()}
                    {assign var="oUser" value=$oAlbum->getUser()}
                    <li>
                        <div class="bk">
                            <a href="{$oAlbum->getUrlFull()}">
                                {if $oImage}
                                <img class="image-100" src="{$oImage->getWebPath('100crop')}" alt="{$oAlbum->getTitle()|escape:'html'}" />
                                {else}
                                    <div class="empty-album"></div>
                                {/if}    
                            </a><br/>
                        </div>
                            <a class="gallery-name" href="{$oAlbum->getUrlFull()}">{$oAlbum->getTitle()|escape:'html'}</a><br/>
                            <a class="user" href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a><br/>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>
    
{include file='paging.tpl' aPaging="$aPaging"}

{include file='footer.tpl'}