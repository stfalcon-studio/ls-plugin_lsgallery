<?php

class PluginLsgallery_ActionMy extends PluginLsgallery_Inherit_ActionMy
{

    protected function RegisterEvent()
    {
        parent::RegisterEvent();
        $this->AddEventPreg('/^.+$/i', '/^album$/i', '/^(page(\d+))?$/i', 'EventAlbums');
    }

    protected function EventAlbums()
    {
        $sUserLogin = $this->sCurrentEvent;

        if (!($this->oUserProfile = $this->User_GetUserByLogin($sUserLogin))) {
            return parent::EventNotFound();
        }

        $iPage = $this->GetParamEventMatch(1, 2) ? $this->GetParamEventMatch(1, 2) : 1;

        $aResult = $this->PluginLsgallery_Album_GetAlbumsPersonalByUser($this->oUserProfile->getId(), $iPage, Config::Get('plugin.lsgallery.album_per_page'));
        $aAlbums = $aResult['collection'];

        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('plugin.lsgallery.album_per_page'), 4, Router::GetPath('my') . $this->oUserProfile->getLogin() . '/album');

        $this->Viewer_Assign('aAlbums', $aAlbums);
        $this->Viewer_Assign('aPaging', $aPaging);

        $this->SetTemplateAction('albums');
    }

    public function EventShutdown()
    {
        if (!$this->oUserProfile) {
            return;
        }
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('lsgallery') . 'css/gallery-style.css');
        $aResult = $this->PluginLsgallery_Album_GetCountAlbumsPersonalByUser($this->oUserProfile->getId());
        $this->Viewer_Assign('iCountAlbumUser', $aResult);
        parent::EventShutdown();
    }

}
