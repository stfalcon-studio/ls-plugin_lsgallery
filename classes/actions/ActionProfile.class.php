<?php

/**
 * Class PluginLsgallery_ActionProfile
 */
class PluginLsgallery_ActionProfile extends PluginLsgallery_Inherit_ActionProfile
{
    /**
     * @var null $iCountMarkedUser
     */
    protected $iCountMarkedUser = null;

    /**
     * Register event
     */
    protected function RegisterEvent()
    {
        parent::RegisterEvent();

        $this->AddEventPreg('/^.+$/i', '/^favourites$/i', '/^images$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventFavouriteImages');

        $this->AddEventPreg('/^.+$/i', '/^created/i', '/^albums$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventCreatedAlbums');
    }

    /**
     * Event favourite images
     *
     * @return string
     */
    protected function EventFavouriteImages()
    {
        // Получаем логин из УРЛа
        $sUserLogin = $this->sCurrentEvent;

        // Проверяем есть ли такой юзер
        if (!($this->oUserProfile = $this->User_GetUserByLogin($sUserLogin))) {
            return parent::EventNotFound();
        }

        // Передан ли номер страницы
        $iPage = $this->GetParamEventMatch(2, 2) ? $this->GetParamEventMatch(2, 2) : 1;

        // Получаем список избранных комментариев
        $aResult = $this->PluginLsgallery_Image_GetImagesFavouriteByUserId($this->oUserProfile->getId(), $iPage, Config::Get('module.comment.per_page'));
        $aImages = $aResult['collection'];

        // Формируем постраничность
        $aPaging = $this->Viewer_MakePaging(
            $aResult['count'],
            $iPage,
            Config::Get('module.comment.per_page'),
            4,
            $this->oUserProfile->getUserWebPath() . '/favourites/images/'
        );

        // Загружаем переменные в шаблон
        $this->Viewer_Assign('aPaging', $aPaging);
        $this->Viewer_Assign('aImages', $aImages);
        $this->Viewer_AddHtmlTitle($this->Lang_Get('user_menu_profile') . ' ' . $this->oUserProfile->getLogin());
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.lsgallery.lsgallery_user_menu_profile_favourites_images'));

        // Устанавливаем шаблон вывода
        $this->SetTemplateAction('images');
    }

    /**
     * Event created albums
     *
     * @return string
     */
    protected function EventCreatedAlbums()
    {
        if (!$this->CheckUserProfile()) {
            return parent::EventNotFound();
        }

        $this->sMenuSubItemSelect = 'albums';

        if ($this->GetParamEventMatch(1, 0) == 'albums') {
            $iPage = $this->GetParamEventMatch(2, 2) ? $this->GetParamEventMatch(2, 2) : 1;
        } else {
            $iPage = $this->GetParamEventMatch(1, 2) ? $this->GetParamEventMatch(1, 2) : 1;
        }

        $aResult = $this->PluginLsgallery_Album_GetAlbumsPersonalByUser($this->oUserProfile->getId(), $iPage, Config::Get('plugin.lsgallery.album_per_page'));
        $aAlbums = $aResult['collection'];

        $aPaging = $this->Viewer_MakePaging(
            $aResult['count'],
            $iPage,
            Config::Get('plugin.lsgallery.album_per_page'),
            4,
            $this->oUserProfile->getUserWebPath() . 'created/albums'
        );

        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.lsgallery.lsgallery_all_created_albums'));

        $this->Viewer_Assign('aAlbums', $aAlbums);
        $this->Viewer_Assign('aPaging', $aPaging);

        $this->SetTemplateAction('albums');
    }

    /**
     * Event shutdown
     *
     * @return void
     */
    public function EventShutdown()
    {
        if (!$this->oUserProfile) {
            return;
        }

        $iCountImageFavourite = $this->PluginLsgallery_Image_GetCountImagesFavouriteByUserId($this->oUserProfile->getId());
        $this->Viewer_Assign('iCountImageFavourite', $iCountImageFavourite);

        $aResult = $this->PluginLsgallery_Album_GetCountAlbumsPersonalByUser($this->oUserProfile->getId());
        $this->Viewer_Assign('iCountAlbumUser', $aResult);

        parent::EventShutdown();
    }
}
