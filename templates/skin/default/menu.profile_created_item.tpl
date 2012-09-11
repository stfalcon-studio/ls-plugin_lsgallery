<li {if $sMenuSubItemSelect=='albums'}class="active"{/if}>
    <a href="{$oUserProfile->getUserWebPath()}created/albums/">{$aLang.plugin.lsgallery.lsgallery_albums}{if $iCountAlbumUser} ({$iCountAlbumUser}){/if}</a>
</li>