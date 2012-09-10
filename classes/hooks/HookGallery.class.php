<?php

class PluginLsgallery_HookGallery extends Hook
{

    public function RegisterHook()
    {
        $this->AddHook('template_main_menu_item', 'Menu');

        $this->AddHook('template_profile_whois_item', 'ProfileAlbums');
        $this->AddHook('template_profile_whois_item', 'ProfilePhoto');

        $this->AddHook('template_menu_profile_favourite_item', 'MenuProfileFavouritePhoto');
        $this->AddHook('template_menu_profile_created_item', 'MenuProfileCreatedAlbum');
    }

    public function Menu()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'main_menu.tpl');
    }

    public function ProfileAlbums($aData)
    {
        $oUser = $aData['oUserProfile'];
        $aResult = $this->PluginLsgallery_Album_GetAlbumsPersonalByUser($oUser->getId(), 1, 4);
        $this->Viewer_Assign("aAlbums", $aResult['collection']);
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'block.albums_list.tpl');
    }

    public function ProfilePhoto($aData)
    {
        $oUser = $aData['oUserProfile'];
        $aResult = $this->PluginLsgallery_Image_GetImagesByUserMarked($oUser->getId(), 1, 4);
        $this->Viewer_Assign("aProfileImages",$aResult['collection']);
        $this->Viewer_Assign("iPhotoCount", $aResult['count']);
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'block.profile_images.tpl');
    }

    public function MenuProfileFavouritePhoto($aData)
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'menu.profile_favourite_item.tpl');
    }

    public function MenuProfileCreatedAlbum($aData)
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'menu.profile_created_item.tpl');
    }

}