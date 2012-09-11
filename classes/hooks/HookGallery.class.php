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
        $this->AddHook('template_main_menu_item', 'Menu');

        $this->AddHook('template_profile_whois_item_after_privat', 'ProfilePhotoMarked');
        $this->AddHook('template_profile_whois_item_after_privat', 'ProfileAlbums');

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
     * Add user photo marked to profile
     *
     * @param array $aData
     *
     * @return string
     */
    public function ProfilePhotoMarked($aData)
    {
        $oUser = $aData['oUserProfile'];
        $aResult = $this->PluginLsgallery_Image_GetImagesByUserMarked($oUser->getId(), 1, 4);
        $this->Viewer_Assign("aProfileImages",$aResult['collection']);
        $this->Viewer_Assign("iPhotoCount", $aResult['count']);
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'block.profile_images.tpl');
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

}