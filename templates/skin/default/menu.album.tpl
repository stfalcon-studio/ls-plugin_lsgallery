<ul class="menu">
	<li {if $sMenuItemSelect=='image'}class="active"{/if}>
		<a href="{router page='gallery'}photo/">{$aLang.lsgallery_photo}</a>
		{if $sMenuItemSelect=='image'}
			<ul class="sub-menu">
				<li {if $sMenuSubItemSelect=='new'}class="active"{/if}><a href="{router page='gallery'}photo/new/">{$aLang.lsgallery_photo_new}</a></li>
				<li {if $sMenuSubItemSelect=='best'}class="active"{/if}><a href="{router page='gallery'}photo/best/">{$aLang.lsgallery_photo_best}</a></li>
			</ul>
		{/if}
	</li>

	<li {if $sMenuItemSelect=='album'}class="active"{/if}>
		<a href="{router page='gallery'}albums/">{$aLang.lsgallery_albums}</a>
		{if $sMenuItemSelect=='album'}
			<ul class="sub-menu">
				<li {if $sMenuSubItemSelect=='all'}class="active"{/if}><a href="{router page='gallery'}albums/">{$aLang.lsgallery_all}</a></li>
				{if $oUserCurrent}
                    <li {if $sMenuSubItemSelect=='my'}class="active"{/if}><a href="{router page='gallery'}albums/my/">{$aLang.lsgallery_my}</a></li>
                {/if}
				
			</ul>
		{/if}
	</li>
    {if $oUserCurrent}
        <li {if $sMenuItemSelect=='create'}class="active"{/if}>
            <a href="{router page='gallery'}create/">{$aLang.lsgallery_create_album_title}</a>
        </li>
    {/if}
</ul>