<?php

class PluginLsgallery_HookGallery extends Hook
{

    public function RegisterHook()
    {
		$this->AddHook('template_admin_action_item', 'MenuAdmin');
        $this->AddHook('template_main_menu', 'Menu');
        $this->AddHook('template_profile_whois_item', 'Profile');
        $this->AddHook('template_profile_whois_item', 'ProfileFoto');
        $this->AddHook('template_menu_profile_profile_item', 'ProfileMenu');
        $this->AddHook('template_menu_profile_my_item', 'MyMenu');
    }

    public function Menu()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'main_menu.tpl');
    }

	public function MenuAdmin()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'admin_menu.tpl');
    }

    public function Profile($aData)
    {
        $oUser = $aData['oUserProfile'];
        $aResult = $this->PluginLsgallery_Album_GetAlbumsPersonalByUser($oUser->getId(), 1, 4);
        $this->Viewer_Assign("aAlbums", $aResult['collection']);
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'block.albums_list.tpl');
    }

    public function ProfileFoto($aData)
    {
        $oUser = $aData['oUserProfile'];
        $aResult = $this->PluginLsgallery_Image_GetImagesByUserMarked($oUser->getId(), 1, 4);
        $this->Viewer_Assign("aProfileImages",$aResult['collection']);
        $this->Viewer_Assign("iPhotoCount", $aResult['count']);
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'block.profile_images.tpl');
    }

    public function ProfileMenu($aData)
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'menu.profile.tpl');
    }

    public function MyMenu($aData)
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'menu.my.tpl');
    }

}