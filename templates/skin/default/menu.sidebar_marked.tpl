<li {if $sAction=='profile' && $aParams[0]=='usermarked'}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}usermarked/">{$aLang.plugin.lsgallery.lsgallery_profile_marked}{if $iCountMarkedUser} ({$iCountMarkedUser}){/if}</a></li>