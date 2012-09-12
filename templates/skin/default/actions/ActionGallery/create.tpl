{include file='header.tpl' menu="album" menu_content="album_edit"}

{if $sEvent=='create'}
    <h2 class="page-header">{$aLang.plugin.lsgallery.lsgallery_create_album_title}</h2>
{else}
    <h2 class="page-header">{$aLang.plugin.lsgallery.lsgallery_admin_album_title}: <a href="{$oAlbumEdit->getUrlFull()}">{$oAlbumEdit->getTitle()}</a></h2>
{/if}

<form id="ls_gallery_create_album" class="wrapper-content" action="" method="POST">
    <p>
        <label for="album_title">{$aLang.plugin.lsgallery.lsgallery_album_title}</label>
        <input type="text" maxlength="64" id="album_title" name="album_title" class="input-text input-width-full" value="{$_aRequest.album_title}" /><br/>
        <span class="note">{$aLang.plugin.lsgallery.lsgallery_album_title_notice}</span>
    </p>
    <p>
        <label for="album_type">{$aLang.plugin.lsgallery.lsgallery_album_type}</label>
        <select id="album_type" name="album_type" class="input-width-200">
            {foreach from=$aLocalizedTypes key=sValue item=sText}
                <option value="{$sValue}" {if ($_aRequest.album_type == $sValue)}selected{/if}>{$sText}</option>
            {/foreach}
        </select><br/>
        <span class="note">{$aLang.plugin.lsgallery.lsgallery_album_type_notice}</span>
    </p>
    <p>
        <label for="album_description">{$aLang.plugin.lsgallery.lsgallery_album_description}</label>
        <textarea rows="5" onkeypress="return imposeMaxLength(this, 512);" id="album_description" name="album_description" class="input-text input-width-full" >{$_aRequest.album_description}</textarea>
    </p>
    <p>
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
        <input type="hidden" name="album_id" value="{$_aRequest.album_id}" />
        <input type="submit" class="button button-primary" name="submit_create_album" value="{$aLang.plugin.lsgallery.lsgallery_save}" />
    </p>
</form>
{include file='footer.tpl'}