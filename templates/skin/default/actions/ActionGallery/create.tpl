{include file='header.tpl' menu="album"}

{if $sEvent=='create'}
    <h2>{$aLang.lsgallery_create_album_title}</h2>
{else}
    {include file="`$sTemplatePathLsgallery`menu.album_edit.tpl"}
{/if}

<form id="ls_gallery_create_album" action="" method="POST">
    <p>
        <label for="album_title">{$aLang.lsgallery_album_title}</label><br/>
        <input type="text" maxlength="64" id="album_title" name="album_title" value="{$_aRequest.album_title}" /><br/>
        <span class="note">{$aLang.lsgallery_album_title_notice}</span>
    </p>
    <p>
        <label for="album_type">{$aLang.lsgallery_album_type}</label><br/>
        <select id="album_type" name="album_type">
            {foreach from=$aLocalizedTypes key=sValue item=sText}
                <option value="{$sValue}" {if ($_aRequest.album_type == $sValue)}selected{/if}>{$sText}</option>
            {/foreach}
        </select><br/>
        <span class="note">{$aLang.lsgallery_album_type_notice}</span>
    </p>
    <p>
        <label for="album_description">{$aLang.lsgallery_album_description}</label><br/>
        <textarea rows="5" onkeypress="return imposeMaxLength(this, 512);" id="album_description" name="album_description">{$_aRequest.album_description}</textarea>
    </p>
    <p>
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
        <input type="hidden" name="album_id" value="{$_aRequest.album_id}" />
        <input type="submit" name="submit_create_album" value="{$aLang.lsgallery_save}" />
    </p>
</form>
{include file='footer.tpl'}