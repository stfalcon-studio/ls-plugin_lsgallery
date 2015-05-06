<?php

/**
 * Class PluginLsgallery_ActionMy
 *
 * @deprecated In future LS version will be rm
 */
class PluginLsgallery_ActionMy extends PluginLsgallery_Inherit_ActionMy
{
    /**
     * Register event
     */
    protected function RegisterEvent()
    {
        parent::RegisterEvent();
        $this->AddEventPreg('/^.+$/i', '/^album$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventAlbums');
    }

    /**
     * Event albums
     *
     * @return string
     */
    protected function EventAlbums()
    {
        $sUserLogin = $this->sCurrentEvent;

        if (!($this->oUserProfile = $this->User_GetUserByLogin($sUserLogin))) {
            return parent::EventNotFound();
        }

        $iPage = $this->GetParamEventMatch(1, 2) ? $this->GetParamEventMatch(1, 2) : 1;

        // Выполняем редирект на новый URL, в новых версиях LS экшен "my" будет удален
        $sPage = $iPage == 1 ? '' : "page{$iPage}/";
        Router::Location($this->oUserProfile->getUserWebPath() . 'created/albums/' . $sPage);
    }
}
