<?php

/**
 * PluginLsgallery_HookGallery
 *
 * Hooks for gallery
 */
class PluginLsgallery_HookGallery extends Hook
{

    /**
     * Register hooks
     */
    public function RegisterHook()
    {
        $this->AddHook('template_admin_action_item', 'MenuAdmin');

        $this->AddHook('template_write_item', 'MenuWriteItem');

        $this->AddHook('template_profile_whois_item_after_privat', 'ProfileAlbums');

        $this->AddHook('template_main_menu_item', 'Menu');
        $this->AddHook('template_menu_profile_favourite_item', 'MenuProfileFavouritePhoto');
        $this->AddHook('template_menu_profile_created_item', 'MenuProfileCreatedAlbum');
    }

    /**
     * Add Gallery link to main menu
     *
     * @return string
     */
    public function Menu()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'main_menu.tpl');
    }

    /**
     * Add recalc link to admin
     *
     * @return string
     */
    public function MenuAdmin()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'admin_menu.tpl');
    }

    /**
     * Add albums block to profile
     *
     * @param array $aData
     *
     * @return string
     */
    public function ProfileAlbums($aData)
    {
        $oUser = $aData['oUserProfile'];
        $aResult = $this->PluginLsgallery_Album_GetAlbumsPersonalByUser($oUser->getId(), 1, 4);
        $this->Viewer_Assign("aAlbums", $aResult['collection']);
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'block.albums_list.tpl');
    }

    /**
     * Add to fav menu link to photos list
     *
     * @param array $aData
     *
     * @return string
     */
    public function MenuProfileFavouritePhoto($aData)
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'menu.profile_favourite_item.tpl');
    }

    /**
     * Add to publish menu link to albums
     *
     * @param array $aData
     *
     * @return string
     */
    public function MenuProfileCreatedAlbum($aData)
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'menu.profile_created_item.tpl');
    }

    public function MenuWriteItem($aData)
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'write_item.tpl');
    }
}