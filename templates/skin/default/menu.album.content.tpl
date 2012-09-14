{if $sMenuItemSelect=='image'}
    <ul class="nav nav-pills mb-30">
        <li {if $sMenuSubItemSelect=='new'}class="active"{/if}><a href="{router page='gallery'}photo/new/">{$aLang.plugin.lsgallery.lsgallery_photo_new}</a></li>
        <li {if $sMenuSubItemSelect=='best'}class="active"{/if}><a href="{router page='gallery'}photo/best/">{$aLang.plugin.lsgallery.lsgallery_photo_best}</a></li>
    </ul>
{/if}
{if $sMenuItemSelect=='album'}
    <ul class="nav nav-pills mb-30">
        <li {if $sMenuSubItemSelect=='all'}class="active"{/if}><a href="{router page='gallery'}albums/">{$aLang.plugin.lsgallery.lsgallery_all}</a></li>
        {if $oUserCurrent}
            <li><a href="{router page='my'}{$oUserCurrent->getLogin()}/album/">{$aLang.plugin.lsgallery.lsgallery_my}</a></li>
        {/if}
    </ul>
{/if}