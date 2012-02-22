<?php

class PluginLsgallery_ActionProfile extends PluginLsgallery_Inherit_ActionProfile
{

    protected function RegisterEvent()
    {
        parent::RegisterEvent();
        $this->AddEventPreg('/^.+$/i', '/^favourites$/i', '/^images$/i', '/^(page(\d+))?$/i', 'EventFavouriteImages');
    }

    protected function EventFavouriteImages()
    {
        /**
         * Получаем логин из УРЛа
         */
        $sUserLogin = $this->sCurrentEvent;
        /**
         * Проверяем есть ли такой юзер
         */
        if (!($this->oUserProfile = $this->User_GetUserByLogin($sUserLogin))) {
            return parent::EventNotFound();
        }
        /**
         * Передан ли номер страницы
         */
        $iPage = $this->GetParamEventMatch(2, 2) ? $this->GetParamEventMatch(2, 2) : 1;
        /**
         * Получаем список избранных комментариев
         */
        $aResult = $this->PluginLsgallery_Image_GetImagesFavouriteByUserId($this->oUserProfile->getId(), $iPage, Config::Get('module.comment.per_page'));
        $aImages = $aResult['collection'];
        /**
         * Формируем постраничность
         */
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('module.comment.per_page'), 4, Router::GetPath('profile') . $this->oUserProfile->getLogin() . '/favourites/comments');
        /**
         * Загружаем переменные в шаблон
         */
        $this->Viewer_Assign('aPaging', $aPaging);
        $this->Viewer_Assign('aImages', $aImages);
        $this->Viewer_AddHtmlTitle($this->Lang_Get('user_menu_profile') . ' ' . $this->oUserProfile->getLogin());
        $this->Viewer_AddHtmlTitle($this->Lang_Get('user_menu_profile_favourites_images'));
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('images');
    }

    public function EventShutdown()
    {
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('lsgallery') . 'css/gallery-style.css');
        if (!$this->oUserProfile) {
            return;
        }
        parent::EventShutdown();
        
        $iCountImageFavourite=$this->PluginLsgallery_Image_GetCountImagesFavouriteByUserId($this->oUserProfile->getId());
        $this->Viewer_Assign('iCountImageFavourite',$iCountImageFavourite);
    }

}