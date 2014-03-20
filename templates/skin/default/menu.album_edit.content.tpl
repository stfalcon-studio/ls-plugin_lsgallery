{if $sEvent!='create'}
    <ul class="nav nav-pills mb-30">
        {if $LS->ACL_AllowUpdateAlbum($oUserCurrent, $oAlbumEdit)}
            <li {if $sMenuSubItemSelect == 'update'}class="active"{/if}><a href="{$oAlbumEdit->getUrlFull('edit')}">{$aLang.plugin.lsgallery.lsgallery_update_album_title}</a></li>
        {/if}
        {if $LS->ACL_AllowAdminAlbumImages($oUserCurrent, $oAlbumEdit)}
            <li {if $sMenuSubItemSelect == 'admin-images'}class="active"{/if}><a href="{$oAlbumEdit->getUrlFull('images')}">{$aLang.plugin.lsgallery.lsgallery_images_album_title}</a></li>
        {/if}
        {if $LS->ACL_AllowUpdateAlbum($oUserCurrent, $oAlbumEdit)}
            <li><a href="{$oAlbumEdit->getUrlFull('delete')}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" title="{$aLang.blog_delete}" onclick="return confirm('{$aLang.plugin.lsgallery.lsgallery_album_delete_confirm}');" >{$aLang.plugin.lsgallery.lsgallery_delete_album_title}</a></li>
        {/if}
    </ul>
{/if}