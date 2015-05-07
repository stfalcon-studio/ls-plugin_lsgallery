<?php

/**
 * Class PluginLsgallery_ActionAjax
 */
class PluginLsgallery_ActionAjax extends ActionPlugin
{

    /**
     * @var $oUserCurrent ModuleUser_EntityUser
     */
    protected $oUserCurrent = null;

    /**
     * Action initialization
     */
    public function Init()
    {
        $this->oUserCurrent = $this->User_GetUserCurrent();
        $this->Viewer_SetResponseAjax('json');
    }

    /**
     * Register routes
     */
    protected function RegisterEvent()
    {
        $this->AddEvent('upload', 'EventUpload');
        $this->AddEvent('deleteimage', 'EventDeleteImage');

        $this->AddEvent('favourite', 'EventFavouriteImage');
        $this->AddEvent('vote', 'EventVoteImage');

        $this->AddEvent('setimagedescription', 'EventSetImageDescription');
        $this->AddEvent('setimagetags', 'EventSetImageTags');
        $this->AddEvent('markascover', 'EventSetImageAsCover');

        $this->AddEvent('getrandomimages', 'EventGetRandomImages');

        $this->AddEvent('getnewimages', 'EventGetNewImages');
        $this->AddEvent('getbestimages', 'EventGetBestImages');

        $this->AddEvent('autocompleteimagetag', 'EventAutocompeleteImageTags');

        $this->AddEvent('moveimage', 'EventMoveImage');
    }

    /**
     * AJAX загрузка фоток
     *
     * @return boolean|string
     */
    protected function EventUpload()
    {
        // В зависимости от типа загрузчика устанавливается тип ответа
        if (getRequest('is_iframe')) {
            $this->Viewer_SetResponseAjax('jsonIframe', false);
        } else {
            $this->Viewer_SetResponseAjax('json');
        }

        if (!$this->User_IsAuthorization()) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));

            return Router::Action('error');
        }

        $iAlbumId = getRequest('album_id');

        /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
        $oAlbum = $this->PluginLsgallery_Album_GetAlbumById($iAlbumId);
        if (!$oAlbum || !$this->ACL_AllowAdminAlbumImages($this->oUserCurrent, $oAlbum)) {
            $this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            $this->Viewer_AssignAjax('success', false);

            return false;
        }

        $sFileTmp = $this->PluginLsgallery_Image_UploadFile();

        if (!$sFileTmp) {
            $this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            $this->Viewer_AssignAjax('success', false);

            return false;
        }

        /**
         * Максимальное количество фото в album
         */
        if ($oAlbum->getImageCount() >= Config::Get('plugin.lsgallery.count_image_max')) {
            $this->Message_AddError($this->Lang_Get('plugin.lsgallery.lsgallery_images_too_much_images', array('MAX' => Config::Get('plugin.lsgallery.count_image_max'))), $this->Lang_Get('error'));
            $this->Viewer_AssignAjax('success', false);

            return false;
        }

        /**
         * Загружаем файл
         */
        $sFile = $this->PluginLsgallery_Image_UploadImage($sFileTmp);
        if ($sFile) {
            $oImage = Engine::GetEntity('PluginLsgallery_ModuleImage_EntityImage');
            $oImage->setUserId($this->oUserCurrent->getId());
            $oImage->setAlbumId($oAlbum->getId());
            $oImage->setFilename($sFile);

            $this->Hook_Run('gallery_image_add_before', array('oImage' => $oImage));
            if ($oImage = $this->PluginLsgallery_Image_AddImage($oImage)) {
                $this->Hook_Run('gallery_image_add_after', array('oImage' => $oImage));

                $this->Viewer_AssignAjax('file', $oImage->getWebPath('100crop'));
                $this->Viewer_AssignAjax('id', $oImage->getId());
                $this->Viewer_AssignAjax('success', true);
                $this->Message_AddNotice($this->Lang_Get('plugin.lsgallery.lsgallery_image_added'), $this->Lang_Get('attention'));
            } else {
                $this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            }
        } else {
            $this->Viewer_AssignAjax('success', false);
        }
    }

    /**
     * AJAX удаление фото
     *
     * @return boolean|string
     */
    protected function EventDeleteImage()
    {
        /**
         * Проверяем авторизован ли юзер
         */
        if (!$this->User_IsAuthorization()) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));

            return Router::Action('error');
        }

        /* @var $oImage PluginLsgallery_ModuleImage_EntityImage */
        $oImage = $this->PluginLsgallery_Image_GetImageById(getRequest('id'));
        if ($oImage) {
            /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
            $oAlbum = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId());
            $oImage->setAlbum($oAlbum);
            if (!$oAlbum || !$this->ACL_AllowUpdateImage($this->oUserCurrent, $oImage)) {
                $this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));

                return false;
            }
            $this->PluginLsgallery_Image_DeleteImage($oImage);

            $this->Message_AddNotice($this->Lang_Get('plugin.lsgallery.lsgallery_image_deleted'), $this->Lang_Get('attention'));

            return true;
        }
        $this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
    }

    /**
     * Сохраняем описание картинки
     *
     * @return boolean
     */
    public function EventSetImageDescription()
    {
        /* @var $oImage PluginLsgallery_ModuleImage_EntityImage */
        $oImage = $this->PluginLsgallery_Image_GetImageById(getRequest('id'));
        if ($oImage) {
            /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
            $oAlbum = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId());
            $oImage->setAlbum($oAlbum);
            if (!$oAlbum || !$this->ACL_AllowUpdateImage($this->oUserCurrent, $oImage)) {
                $this->Message_AddError($this->Lang_Get('no_access'), $this->Lang_Get('error'));

                return false;
            }

            $oImage->setDescription(getRequest('text'));
            $this->PluginLsgallery_Image_UpdateImage($oImage);
        }
    }

    /**
     * Отмечаем картинку как обложку
     *
     * @return boolean
     */
    public function EventSetImageAsCover()
    {
        /* @var $oImage PluginLsgallery_ModuleImage_EntityImage */
        $oImage = $this->PluginLsgallery_Image_GetImageById(getRequest('id'));
        if ($oImage) {
            /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
            $oAlbum = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId());
            if (!$oAlbum || !$this->ACL_AllowUpdateAlbum($this->oUserCurrent, $oAlbum)) {
                $this->Message_AddError($this->Lang_Get('no_access'), $this->Lang_Get('error'));

                return false;
            }

            $oAlbum->setCoverId($oImage->getId());
            $this->PluginLsgallery_Album_UpdateAlbum($oAlbum);
        }
    }

    /**
     * Сохраняем теги картинки
     *
     * @return boolean
     */
    public function EventSetImageTags()
    {
        $sTags = getRequest('tags', null, 'post');

        $aTags       = explode(',', $sTags);
        $aTagsNew    = array();
        $aTagsNewLow = array();
        foreach ($aTags as $sTag) {
            $sTag = trim($sTag);
            if (func_check($sTag, 'text', 2, 50) and !in_array(mb_strtolower($sTag, 'UTF-8'), $aTagsNewLow)) {
                $aTagsNew[]    = $sTag;
                $aTagsNewLow[] = mb_strtolower($sTag, 'UTF-8');
            }
        }
        if (!count($aTagsNew)) {
            $sTags = '';
        } else {
            $sTags = join(',', $aTagsNew);
        }

        /* @var $oImage PluginLsgallery_ModuleImage_EntityImage */
        $oImage = $this->PluginLsgallery_Image_GetImageById(getRequest('id'));
        if ($oImage) {
            /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
            $oAlbum = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId());
            $oImage->setAlbum($oAlbum);
            if (!$oAlbum || !$this->ACL_AllowUpdateImage($this->oUserCurrent, $oImage)) {
                $this->Message_AddError($this->Lang_Get('no_access'), $this->Lang_Get('error'));

                return false;
            }
            $oImage->setImageTags($sTags);
            $this->PluginLsgallery_Image_UpdateImage($oImage);
        }
    }

    /**
     * Автокомплит тегов картинок
     *
     * @return void
     */
    public function EventAutocompeleteImageTags()
    {
        if (!($sValue = getRequest('value', null, 'post'))) {
            return;
        }

        $aItems = array();
        $aTags  = $this->PluginLsgallery_Image_GetImageTagsByLike($sValue, 10);
        foreach ($aTags as $oTag) {
            $aItems[] = $oTag->getText();
        }
        $this->Viewer_AssignAjax('aItems', $aItems);
    }

    /**
     * Сохраняем картинку в избранное
     */
    protected function EventFavouriteImage()
    {
        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));

            return;
        }

        $iType = getRequest('type', null, 'post');
        if (!in_array($iType, array('1', '0'))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));

            return;
        }

        /* @var $oImage PluginLsgallery_ModuleImage_EntityImage */
        if (!$oImage = $this->PluginLsgallery_Image_GetImageById(getRequest('idImage', null, 'post'))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));

            return;
        }
        /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
        if (!$oAlbum = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId())) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));

            return;
        }

        if (!$this->ACL_AllowViewAlbumImages($this->oUserCurrent, $oAlbum)) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));

            return;
        }

        $oFavouriteImage = $this->PluginLsgallery_Image_GetFavouriteImage($oImage->getId(), $this->oUserCurrent->getId());
        if (!$oFavouriteImage && $iType) {
            $oFavouriteImageNew = Engine::GetEntity('Favourite', array(
                    'target_id'      => $oImage->getId(),
                    'user_id'        => $this->oUserCurrent->getId(),
                    'target_type'    => 'image',
                    'target_publish' => true
                )
            );
            $oImage->setCountFavourite($oImage->getCountFavourite() + 1);
            if ($this->Favourite_AddFavourite($oFavouriteImageNew)
                && $this->PluginLsgallery_Image_UpdateImage($oImage)
            ) {
                $this->Message_AddNoticeSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_favourite_add_ok'), $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('bState', true);
                $this->Viewer_AssignAjax('iCount', $oImage->getCountFavourite());
            } else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));

                return;
            }
        }
        if (!$oFavouriteImage && !$iType) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_favourite_add_no'), $this->Lang_Get('error'));

            return;
        }
        if ($oFavouriteImage && $iType) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_favourite_add_already'), $this->Lang_Get('error'));

            return;
        }
        if ($oFavouriteImage && !$iType) {
            $oImage->setCountFavourite($oImage->getCountFavourite() - 1);
            if ($this->Favourite_DeleteFavourite($oFavouriteImage)
                && $this->PluginLsgallery_Image_UpdateImage($oImage)
            ) {
                $this->Message_AddNoticeSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_favourite_del_ok'), $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('bState', false);
                $this->Viewer_AssignAjax('iCount', $oImage->getCountFavourite());
            } else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));

                return;
            }
        }
    }

    /**
     * Голосуем за картинку
     *
     * @return void
     */
    public function EventVoteImage()
    {
        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));

            return;
        }

        /* @var $oImage PluginLsgallery_ModuleImage_EntityImage */
        if (!$oImage = $this->PluginLsgallery_Image_GetImageById(getRequest('idImage', null, 'post'))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));

            return;
        }
        /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
        if (!$oAlbum = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId())) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));

            return;
        }

        if (!$this->ACL_AllowViewAlbumImages($this->oUserCurrent, $oAlbum)) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));

            return;
        }

        if ($oImage->getUserId() == $this->oUserCurrent->getId()) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_vote_error_self'), $this->Lang_Get('attention'));

            return;
        }

        if ($oImageVote = $this->Vote_GetVote($oImage->getId(), 'image', $this->oUserCurrent->getId())) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_vote_error_already'), $this->Lang_Get('attention'));

            return;
        }

        if (strtotime($oImage->getDateAdd()) <= time() - Config::Get('acl.vote.image.limit_time')) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_vote_error_time'), $this->Lang_Get('attention'));

            return;
        }

        $iValue = getRequest('value', null, 'post');
        if (!in_array($iValue, array('1', '-1', '0'))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('attention'));

            return;
        }

        if ($this->oUserCurrent->getRating() < Config::Get('acl.vote.image.rating')) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_vote_error_acl'), $this->Lang_Get('attention'));

            return;
        }

        $oImageVote = Engine::GetEntity('Vote');
        $oImageVote->setTargetId($oImage->getId());
        $oImageVote->setTargetType('image');
        $oImageVote->setVoterId($this->oUserCurrent->getId());
        $oImageVote->setDirection($iValue);
        $oImageVote->setDate(date("Y-m-d H:i:s"));
        $iVal = 0;
        if ($iValue != 0) {
            $iVal = (float) $this->Rating_VoteImage($this->oUserCurrent, $oImage, $iValue);
        }
        $oImageVote->setValue($iVal);
        $oImage->setCountVote($oImage->getCountVote() + 1);
        if ($iValue == 1) {
            $oImage->setCountVoteUp($oImage->getCountVoteUp() + 1);
        } elseif ($iValue == -1) {
            $oImage->setCountVoteDown($oImage->getCountVoteDown() + 1);
        } elseif ($iValue == 0) {
            $oImage->setCountVoteAbstain($oImage->getCountVoteAbstain() + 1);
        }

        $this->Hook_Run('gallery_image_vote_before', array('oImageVote' => $oImageVote, 'oImage' => $oImage));
        if ($this->Vote_AddVote($oImageVote) && $this->PluginLsgallery_Image_UpdateImage($oImage)) {
            $this->Hook_Run('gallery_image_vote_after', array('oImageVote' => $oImageVote, 'oImage' => $oImage));

            if ($iValue) {
                $this->Message_AddNoticeSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_vote_ok'), $this->Lang_Get('attention'));
            } else {
                $this->Message_AddNoticeSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_vote_ok_abstain'), $this->Lang_Get('attention'));
            }
            $this->Viewer_AssignAjax('iRating', $oImage->getRating());
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));

            return;
        }
    }

    /**
     * Получаем случайные картинки
     */
    protected function EventGetRandomImages()
    {
        $aRandomImages = $this->PluginLsgallery_Image_GetRandomImages(Config::Get('plugin.lsgallery.images_random'));
        $sHtml         = '';
        foreach ($aRandomImages as $oImage) {
            $sHtml .= '<li><a href="' . $oImage->getUrlFull() . '"><img src="' . $oImage->getWebPath('100crop')
                      . '" alt="Image" /></a></li>';
        }

        $this->Viewer_AssignAjax('sHtml', $sHtml);
    }

    /**
     * Получаем последние картинки
     */
    public function EventGetNewImages()
    {
        $aResult = $this->PluginLsgallery_Image_GetImagesNew(1, Config::Get('plugin.lsgallery.image_row'));
        $aImages = $aResult['collection'];
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('aImages', $aImages);
        $oViewer->Assign('sType', 'new');
        $oViewer->Assign('oUserCurrent', $this->oUserCurrent);
        $sTextResult = $oViewer->Fetch(Plugin::GetTemplatePath('lsgallery') . "block.stream_photo.tpl");
        $this->Viewer_AssignAjax('sText', $sTextResult);
    }

    /**
     * Получаем лучшие картинки
     */
    public function EventGetBestImages()
    {
        $aResult = $this->PluginLsgallery_Image_GetImagesBest(1, Config::Get('plugin.lsgallery.image_row'));

        $aImages = $aResult['collection'];
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('aImages', $aImages);
        $oViewer->Assign('sType', 'best');
        $oViewer->Assign('oUserCurrent', $this->oUserCurrent);
        $sTextResult = $oViewer->Fetch(Plugin::GetTemplatePath('lsgallery') . "block.stream_photo.tpl");
        $this->Viewer_AssignAjax('sText', $sTextResult);
    }

    /**
     * Перемещаем изображение в другой альбом
     *
     * @return boolean|void
     */
    public function EventMoveImage()
    {
        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));

            return;
        }
        $sImageId = getRequest('idImage');
        $sAlbumId = getRequest('idAlbum');

        /* @var $oImage PluginLsgallery_ModuleImage_EntityImage */
        if (!$oImage = $this->PluginLsgallery_Image_GetImageById($sImageId)) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_not_found'), $this->Lang_Get('error'));

            return;
        }

        /* @var $oAlbumFrom PluginLsgallery_ModuleAlbum_EntityAlbum */
        $oAlbumFrom = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId());

        /* @var $oAlbumTo PluginLsgallery_ModuleAlbum_EntityAlbum */
        if (!$oAlbumTo = $this->PluginLsgallery_Album_GetAlbumById($sAlbumId)) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_not_found'), $this->Lang_Get('error'));

            return;
        }
        if (!$this->ACL_AllowAdminAlbumImages($this->oUserCurrent, $oAlbumFrom)
            || !$this->ACL_AllowAdminAlbumImages($this->oUserCurrent, $oAlbumTo)
        ) {
            $this->Message_AddError($this->Lang_Get('no_access'), $this->Lang_Get('error'));

            return false;
        }

        $this->PluginLsgallery_Image_MoveImage($oImage, $oAlbumFrom, $oAlbumTo);
        $this->Message_AddNoticeSingle($this->Lang_Get('plugin.lsgallery.lsgallery_image_moved'), $this->Lang_Get('attention'));
    }
}
