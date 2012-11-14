<?php

class PluginLsgallery_ModuleImage extends Module
{

    /**
     *
     * @var PluginLsgallery_ModuleImage_MapperImage
     */
    protected $oMapper;

    /**
     *
     * @var ModuleUser_EntityUser
     */
    protected $oUserCurrent = null;

    public function Init()
    {
        $this->oMapper = Engine::GetMapper(__CLASS__);
        $this->oUserCurrent = $this->User_GetUserCurrent();
    }

    /**
     * Add image
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     * @return PluginLsgallery_ModuleImage_EntityImage|boolean
     */
    public function AddImage($oImage)
    {
        $oImage->setDateAdd();
        if ($sId = $this->oMapper->AddImage($oImage)) {
            $oImage = $this->GetImageById($sId);
            /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */

            $oAlbum = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId());
            if (!$oAlbum->getCoverId()) {
                $oAlbum->setCoverId($sId);
            }

            $oAlbum->setImageCount($oAlbum->getImageCount() + 1);
            $this->PluginLsgallery_Album_UpdateAlbum($oAlbum);
            $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('image_new'));
            return $oImage;
        }
        return false;
    }

    /**
     * Edit image
     *
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     * @return boolean
     */
    public function UpdateImage($oImage)
    {
        $oImageOld = $this->GetImageById($oImage->getId());
        $oImage->setDateEdit();
        /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
        $oAlbum = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId());
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("image_update"));
        $this->Cache_Delete("image_{$oImage->getId()}");
        $this->oMapper->UpdateImage($oImage);

        if ($oImage->getImageTags() != $oImageOld->getImageTags()) {
            /**
             * Обновляем теги
             */
            $aTags = explode(',', $oImage->getImageTags());
            $this->DeleteImageTagsByImageId($oImage->getId());

            if ($oAlbum->getType() == PluginLsgallery_ModuleAlbum_EntityAlbum::TYPE_OPEN) {
                foreach ($aTags as $sTag) {
                    $oTag = Engine::GetEntity('PluginLsgallery_ModuleImage_EntityImageTag');
                    $oTag->setImageId($oImage->getId());
                    $oTag->setAlbumId($oImage->getAlbumId());
                    $oTag->setText(trim($sTag));
                    $this->oMapper->AddImageTag($oTag);
                }
            }
        }
        return true;
    }

    /**
     * Get image tags by like
     * @param string $sTag
     * @param int $iLimit
     * @return \PluginLsgallery_ModuleImage_EntityImageTag
     */
    public function GetImageTagsByLike($sTag, $iLimit)
    {
        if (false === ($data = $this->Cache_Get("image_like_{$sTag}_{$iLimit}"))) {
            $data = $this->oMapper->GetImageTagsByLike($sTag, $iLimit);
            $this->Cache_Set($data, "image_like_{$sTag}_{$iLimit}", array("image_update", "image_new"), 60 * 60 * 24 * 3);
        }
        return $data;
    }

    /**
     * Delete image tags by image
     *
     * @param string $sImageId
     * @return boolean
     */
    public function DeleteImageTagsByImageId($sImageId)
    {
        return $this->oMapper->DeleteImageTagsByImageId($sImageId);
    }

    /**
     * Upload Image
     * @param array $aFile
     * @return string|boolean
     */
    public function UploadImage($aFile)
    {
        if (!is_array($aFile) || !isset($aFile['tmp_name'])) {
            return false;
        }

        $sFileName = func_generator(10);
        $sPath = Config::Get('path.uploads.images') . '/lsgallery/' . date('Y/m/d') . '/';

        if (!is_dir(Config::Get('path.root.server') . $sPath)) {
            mkdir(Config::Get('path.root.server') . $sPath, 0755, true);
        }

        $sFileTmp = Config::Get('path.root.server') . $sPath . $sFileName;
        if (!move_uploaded_file($aFile['tmp_name'], $sFileTmp)) {
            return false;
        }


        $aParams = $this->Image_BuildParams('lsgallery');

        $oImage = new LiveImage($sFileTmp);
        /**
         * Если объект изображения не создан,
         * возвращаем ошибку
         */
        if ($sError = $oImage->get_last_error()) {
            // Вывод сообщения об ошибки, произошедшей при создании объекта изображения
            $this->Message_AddError($sError, $this->Lang_Get('error'));
            @unlink($sFileTmp);
            return false;
        }

        /**
         * Превышает максимальные размеры из конфига
         */
        if (($oImage->get_image_params('width') > Config::Get('view.img_max_width')) or ($oImage->get_image_params('height') > Config::Get('view.img_max_height'))) {
            $this->Message_AddError($this->Lang_Get('topic_photoset_error_size'), $this->Lang_Get('error'));
            @unlink($sFileTmp);
            return false;
        }

        // Добавляем к загруженному файлу расширение
        $sFile = $sFileTmp . '.' . $oImage->get_image_params('format');
        rename($sFileTmp, $sFile);

        $aSizes = Config::Get('plugin.lsgallery.size');
        foreach ($aSizes as $aSize) {
            // Для каждого указанного в конфиге размера генерируем картинку
            $sNewFileName = $sFileName . '_' . $aSize['w'];
            $oImage = new LiveImage($sFile);
            if ($aSize['crop']) {
                $this->Image_CropProportion($oImage, $aSize['w'], $aSize['h'], true);
                $sNewFileName .= 'crop';
            }
            $this->Image_Resize($sFile, $sPath, $sNewFileName, Config::Get('view.img_max_width'), Config::Get('view.img_max_height'), $aSize['w'], $aSize['h'], true, $aParams, $oImage);
        }
        $sWebPath = $this->Image_GetWebPath($sFile);
        return str_ireplace(Config::Get('path.root.web'), '', $sWebPath);
    }

    /**
     * Delete image
     *
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     * @return boolean
     */
    public function DeleteImage($oImage)
    {
        $oAlbum = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId());

        if (!$this->oMapper->DeleteImage($oImage->getId())) {
            return false;
        }

        if ($oAlbum->getCoverId() == $oImage->getId()) {
            $oAlbum->setCoverId(null);
        }

        $oAlbum->setImageCount($oAlbum->getImageCount() - 1);
        $this->PluginLsgallery_Album_UpdateAlbum($oAlbum);

        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("image_update"));
        $this->Cache_Delete("image_{$oImage->getId()}");

        $this->DeleteImageFiles($oImage);

        $this->Vote_DeleteVoteByTarget($oImage->getId(), 'image');
        $this->Favourite_DeleteFavouriteByTargetId($oImage->getId(), 'image');
        $this->Comment_DeleteCommentByTargetId($oImage->getId(), 'image');

        return true;
    }

    /**
     * Delete image files
     *
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     */
    public function DeleteImageFiles($oImage)
    {
        @unlink($this->Image_GetServerPath(rtrim(Config::Get('path.root.web'), '/') . $oImage->getWebPath()));

        $aSizes = Config::Get('plugin.lsgallery.size');
        // Удаляем все сгенерированные миниатюры основываясь на данных из конфига.
        foreach ($aSizes as $aSize) {
            $sSize = $aSize['w'];
            if ($aSize['crop']) {
                $sSize .= 'crop';
            }
            @unlink($this->Image_GetServerPath(rtrim(Config::Get('path.root.web'), '/') . $oImage->getWebPath($sSize)));
        }
    }

    /**
     * Get images by album id per page
     *
     * @param int $iAlbumId
     * @param int $iPage
     * @param int $iPerPage
     * @return PluginLsgallery_ModuleImage_EntityImage
     */
    public function GetImagesByAlbumId($iAlbumId, $iPage = 0, $iPerPage = 0)
    {
        if ($iAlbumId instanceof PluginLsgallery_ModuleAlbum_EntityAlbum) {
            $iAlbumId = $iAlbumId->getId();
        }

        $aFilter = array(
            'album_id' => $iAlbumId,
        );
        return $this->GetImagesByFilter($aFilter, $iPage, $iPerPage);
    }

    /**
     * Get images by filter
     *
     * @param array $aFilter
     * @param int $iPage
     * @param int $iPerPage
     * @param array $aAllowData
     * @param boolean $bOnlyIds
     * @return PluginLsgallery_ModuleImage_EntityImage
     */
    public function GetImagesByFilter($aFilter, $iPage = 0, $iPerPage = 0, $aAllowData = array('user' => array(), 'vote', 'favourite', 'comment_new'), $bOnlyIds = false)
    {
        $s = serialize($aFilter);
        if (false === ($data = $this->Cache_Get("image_filter_{$s}_{$iPage}_{$iPerPage}"))) {
            $data = ($iPage * $iPerPage != 0) ? array(
                'collection' => $this->oMapper->GetImages($aFilter, $iCount, $iPage, $iPerPage),
                'count' => $iCount
                    ) : array(
                'collection' => $this->oMapper->GetAllImages($aFilter),
                'count' => $this->GetCountImagesByFilter($aFilter)
                    );
            $this->Cache_Set($data, "image_filter_{$s}_{$iPage}_{$iPerPage}", array('image_update', 'image_new'), 60 * 60 * 24 * 3);
        }
        if (!$bOnlyIds) {
            $data['collection'] = $this->GetImagesAdditionalData($data['collection'], $aAllowData);
        }
        return $data;
    }

    /**
     * Get count images by filter
     *
     * @param array $aFilter
     * @return int
     */
    public function GetCountImagesByFilter($aFilter)
    {
        $s = serialize($aFilter);
        if (false === ($data = $this->Cache_Get("image_count_{$s}"))) {
            $data = $this->oMapper->GetCountImages($aFilter);
            $this->Cache_Set($data, "image_count_{$s}", array('image_update', 'image_new'), 60 * 60 * 24 * 1);
        }
        return $data;
    }

    /**
     * Get image by Id
     *
     * @param string $sImageId
     * @return PluginLsgallery_ModuleImage_EntityImage|null
     */
    public function GetImageById($sImageId)
    {
        $aImages = $this->GetImagesAdditionalData($sImageId);
        if (isset($aImages[$sImageId])) {
            return $aImages[$sImageId];
        }
        return null;
    }

    /**
     * Get images additional data
     *
     * @param array $aImagesId
     * @param array $aAllowData
     * @return array
     */
    public function GetImagesAdditionalData($aImagesId, $aAllowData = array('user' => array(), 'vote', 'favourite', 'comment_new'))
    {
        func_array_simpleflip($aAllowData);
        if (!is_array($aImagesId)) {
            $aImagesId = array($aImagesId);
        }

        $aImages = $this->GetImagesByArrayId($aImagesId);

        $aUserId = array();

        foreach ($aImages as $oImage) {
            /* @var $oImage PluginLsgallery_ModuleImage_EntityImage */
            if (isset($aAllowData['user'])) {
                $aUserId[] = $oImage->getUserId();
            }
        }
        $aUsers = isset($aAllowData['user']) && is_array($aAllowData['user']) ? $this->User_GetUsersAdditionalData($aUserId, $aAllowData['user']) : $this->User_GetUsersAdditionalData($aUserId);

        if (isset($aAllowData['favourite']) && $this->oUserCurrent) {
            $aFavouriteImages = $this->GetFavouriteImagesByArray($aImagesId, $this->oUserCurrent->getId());
        }

        if (isset($aAllowData['comment_new']) && $this->oUserCurrent) {
            $aImagesRead = $this->GetImagesReadByArray($aImagesId, $this->oUserCurrent->getId());
        }

        if (isset($aAllowData['vote']) && $this->oUserCurrent) {
            $aImagesVote = $this->Vote_GetVoteByArray($aImagesId, 'image', $this->oUserCurrent->getId());
        }

        foreach ($aImages as $oImage) {
            if (isset($aUsers[$oImage->getUserId()])) {
                $oImage->setUser($aUsers[$oImage->getUserId()]);
            } else {
                $oImage->setUser(null);
            }

            if (isset($aFavouriteImages[$oImage->getId()])) {
                $oImage->setIsFavourite(true);
            } else {
                $oImage->setIsFavourite(false);
            }

            if (isset($aImagesRead[$oImage->getId()])) {
                $oImage->setCountCommentNew($oImage->getCountComment() - $aImagesRead[$oImage->getId()]->getCommentCountLast());
                $oImage->setDateRead($aImagesRead[$oImage->getId()]->getDateRead());
            } else {
                $oImage->setCountCommentNew(0);
                $oImage->setDateRead(date("Y-m-d H:i:s"));
            }

            if (isset($aImagesVote[$oImage->getId()])) {
                $oImage->setVote($aImagesVote[$oImage->getId()]);
            } else {
                $oImage->setVote(null);
            }
        }

        return $aImages;
    }

    /**
     * Get favourite image
     *
     * @param string $sImageId
     * @param type $sUserId
     * @return ModuleFavourite_EntityFavourite|null
     */
    public function GetFavouriteImage($sImageId, $sUserId)
    {
        return $this->Favourite_GetFavourite($sImageId, 'image', $sUserId);
    }

    /**
     * Get favourite images
     *
     * @param array $aImages
     * @param string $sUserId
     * @return \ModuleFavourite_EntityFavourite
     */
    public function GetFavouriteImagesByArray($aImages, $sUserId)
    {
        return $this->Favourite_GetFavouritesByArray($aImages, 'image', $sUserId);
    }

    /**
     * Get images by array Id
     *
     * @param array $aImageId
     * @return array
     */
    public function GetImagesByArrayId($aImageId)
    {
        if (!$aImageId) {
            return array();
        }

        if (Config::Get('sys.cache.solid')) {
            return $this->GetImagesByArrayIdSolid($aImageId);
        }

        if (!is_array($aImageId)) {
            $aImageId = array($aImageId);
        }

        $aImageId = array_unique($aImageId);
        $aImages = array();
        $aImagesIdNotNeedQuery = array();
        /**
         * Делаем мульти-запрос к кешу
         */
        $aCacheKeys = func_build_cache_keys($aImageId, 'image_');
        if (false !== ($data = $this->Cache_Get($aCacheKeys))) {
            /**
             * проверяем что досталось из кеша
             */
            foreach ($aCacheKeys as $sValue => $sKey) {
                if (array_key_exists($sKey, $data)) {
                    if ($data[$sKey]) {
                        $aImages[$data[$sKey]->getId()] = $data[$sKey];
                    } else {
                        $aImagesIdNotNeedQuery[] = $sValue;
                    }
                }
            }
        }

        $aImageIdNeedQuery = array_diff($aImageId, array_keys($aImages));
        $aImageIdNeedQuery = array_diff($aImageIdNeedQuery, $aImagesIdNotNeedQuery);
        $aImageIdNeedStore = $aImageIdNeedQuery;
        if ($data = $this->oMapper->GetImagesByArrayId($aImageIdNeedQuery)) {
            foreach ($data as $oImage) {
                /**
                 * Добавляем к результату и сохраняем в кеш
                 */
                $aImages[$oImage->getId()] = $oImage;
                $this->Cache_Set($oImage, "image_{$oImage->getId()}", array(), 60 * 60 * 24 * 4);
                $aImageIdNeedStore = array_diff($aImageIdNeedStore, array($oImage->getId()));
            }
        }
        /**
         * Сохраняем в кеш запросы не вернувшие результата
         */
        foreach ($aImageIdNeedStore as $sId) {
            $this->Cache_Set(null, "image_{$sId}", array(), 60 * 60 * 24 * 4);
        }
        /**
         * Сортируем результат согласно входящему массиву
         */
        $aImages = func_array_sort_by_keys($aImages, $aImageId);
        return $aImages;
    }

    /**
     * Get images by array id from solid cache
     *
     * @param array $aImagesId
     * @return array
     */
    public function GetImagesByArrayIdSolid($aImagesId)
    {
        if (!is_array($aImagesId)) {
            $aImagesId = array($aImagesId);
        }
        $aImagesId = array_unique($aImagesId);
        $aImages = array();
        $s = join(',', $aImagesId);
        if (false === ($data = $this->Cache_Get("image_id_{$s}"))) {
            $data = $this->oMapper->GetImagesByArrayId($aImagesId);
            foreach ($data as $oImage) {
                $aImages[$oImage->getId()] = $oImage;
            }
            $this->Cache_Set($aImages, "image_id_{$s}", array("image_update"), 60 * 60 * 24 * 1);
            return $aImages;
        }
        return $data;
    }

    /**
     * Set image read
     *
     * @param PluginLsgallery_ModuleImage_EntityImageRead $oImageRead
     * @return boolean
     */
    public function SetimageRead($oImageRead)
    {
        if ($this->GetImageRead($oImageRead->getImageId(), $oImageRead->getUserId())) {
            $this->Cache_Delete("image_read_{$oImageRead->getImageId()}_{$oImageRead->getUserId()}");
            $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("image_read_user_{$oImageRead->getUserId()}"));
            $this->oMapper->UpdateImageRead($oImageRead);
        } else {
            $this->Cache_Delete("image_read_{$oImageRead->getImageId()}_{$oImageRead->getUserId()}");
            $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("image_read_user_{$oImageRead->getUserId()}"));
            $this->oMapper->AddImageRead($oImageRead);
        }
        return true;
    }

    /**
     * Get image read
     *
     * @param string $sImageId
     * @param string $sUserId
     * @return PluginLsgallery_ModuleImage_EntityImageRead|null
     */
    public function GetImageRead($sImageId, $sUserId)
    {
        $data = $this->GetImagesReadByArray($sImageId, $sUserId);
        if (isset($data[$sImageId])) {
            return $data[$sImageId];
        }
        return null;
    }

    /**
     * Delete image read by array of image id
     *
     * @param array|int $aImageId
     * @return boolean
     */
    public function DeleteImageReadByArrayId($aImageId)
    {
        if (!is_array($aImageId))
            $aImageId = array($aImageId);
        return $this->oMapper->DeleteImageReadByArrayId($aImageId);
    }

    /**
     * Get images read by images and user
     *
     * @param array $aImageId
     * @param string $sUserId
     * @return \PluginLsgallery_ModuleImage_EntityImageRead
     */
    public function GetImagesReadByArray($aImageId, $sUserId)
    {
        if (!$aImageId) {
            return array();
        }
        if (Config::Get('sys.cache.solid')) {
            return $this->GetImagesReadByArraySolid($aImageId, $sUserId);
        }
        if (!is_array($aImageId)) {
            $aImageId = array($aImageId);
        }
        $aImageId = array_unique($aImageId);
        $aImagesRead = array();
        $aImageIdNotNeedQuery = array();
        /**
         * Делаем мульти-запрос к кешу
         */
        $aCacheKeys = func_build_cache_keys($aImageId, 'image_read_', '_' . $sUserId);
        if (false !== ($data = $this->Cache_Get($aCacheKeys))) {
            /**
             * проверяем что досталось из кеша
             */
            foreach ($aCacheKeys as $sValue => $sKey) {
                if (array_key_exists($sKey, $data)) {
                    if ($data[$sKey]) {
                        $aImagesRead[$data[$sKey]->getImageId()] = $data[$sKey];
                    } else {
                        $aImageIdNotNeedQuery[] = $sValue;
                    }
                }
            }
        }

        $aImageIdNeedQuery = array_diff($aImageId, array_keys($aImagesRead));
        $aImageIdNeedQuery = array_diff($aImageIdNeedQuery, $aImageIdNotNeedQuery);
        $aImageIdNeedStore = $aImageIdNeedQuery;
        if ($data = $this->oMapper->GetImagesReadByArray($aImageIdNeedQuery, $sUserId)) {
            foreach ($data as $oImageRead) {
                /**
                 * Добавляем к результату и сохраняем в кеш
                 */
                $aImagesRead[$oImageRead->getImageId()] = $oImageRead;
                $this->Cache_Set($oImageRead, "image_read_{$oImageRead->getImageId()}_{$oImageRead->getUserId()}", array(), 60 * 60 * 24 * 4);
                $aImageIdNeedStore = array_diff($aImageIdNeedStore, array($oImageRead->getImageId()));
            }
        }
        /**
         * Сохраняем в кеш запросы не вернувшие результата
         */
        foreach ($aImageIdNeedStore as $sId) {
            $this->Cache_Set(null, "image_read_{$sId}_{$sUserId}", array(), 60 * 60 * 24 * 4);
        }
        /**
         * Сортируем результат согласно входящему массиву
         */
        $aImagesRead = func_array_sort_by_keys($aImagesRead, $aImageId);
        return $aImagesRead;
    }

    /**
     * Get images read by images and user from solid cache
     *
     * @param array $aImageId
     * @param string $sUserId
     * @return \PluginLsgallery_ModuleImage_EntityImageRead
     */
    public function GetImagesReadByArraySolid($aImageId, $sUserId)
    {
        if (!is_array($aImageId)) {
            $aImageId = array($aImageId);
        }
        $aImageId = array_unique($aImageId);
        $aImagesRead = array();
        $s = join(',', $aImageId);
        if (false === ($data = $this->Cache_Get("image_read_{$sUserId}_id_{$s}"))) {
            $data = $this->oMapper->GetImagesReadByArray($aImageId, $sUserId);
            foreach ($data as $oImageRead) {
                $aImagesRead[$oImageRead->getImageId()] = $oImageRead;
            }
            $this->Cache_Set($aImagesRead, "image_read_{$sUserId}_id_{$s}", array("image_read_user_{$sUserId}"), 60 * 60 * 24 * 1);
            return $aImagesRead;
        }
        return $data;
    }

    /**
     * Inreace image comment count
     *
     * @param string $sImageId
     * @return boolean
     */
    public function IncreaseImageCountComment($sImageId)
    {
        $this->Cache_Delete("image_{$sImageId}");
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("image_update"));
        return $this->oMapper->IncreaseImageCountComment($sImageId);
    }

    /**
     * Get image of day
     *
     * @return PluginLsgallery_ModuleImage_EntityImage|null
     */
    public function GetImageOfDay()
    {
        $sDate = date("Y-m-d 00:00:00");
        $aFilter = array(
            'album_type' => array(
                'open'
            ),
            'image_new' => $sDate,
            'order' => 'image_rating desc'
        );

        $aResult = $this->GetImagesByFilter($aFilter, 1, 1);
        if ($aResult['count']) {
            return array_shift($aResult['collection']);
        }

        return null;
    }

    /**
     * Get new images
     *
     * @param int $iPage
     * @param int $iPerPage
     * @return \PluginLsgallery_ModuleImage_EntityImage
     */
    public function GetImagesNew($iPage, $iPerPage)
    {
        $sDate = date("Y-m-d H:00:00", time() - Config::Get('plugin.lsgallery.images_new_time'));
        $aFilter = array(
            'album_type' => array(
                'open',
            ),
            'image_new' => $sDate,
        );

        if ($this->oUserCurrent) {
            $aFriends = $this->User_GetUsersFriend($this->oUserCurrent->getId());
            if ($aFriends['count']) {
                $aFilter['album_type']['friend'] = array_keys($aFriends['collection']);
            }
        }
        return $this->GetImagesByFilter($aFilter, $iPage, $iPerPage);
    }

    /**
     * Get best images
     *
     * @param int $iPage
     * @param int $iPerPage
     * @return \PluginLsgallery_ModuleImage_EntityImage
     */
    public function GetImagesBest($iPage, $iPerPage)
    {
        $aFilter = array(
            'album_type' => array(
                'open',
            ),
            'image_rating' => array(
                'value' => Config::Get('plugin.lsgallery.images_best'),
                'type' => 'top'
            ),
            'order' => 'image_rating DESC'
        );

        if ($this->oUserCurrent) {
            $aFriends = $this->User_GetUsersFriend($this->oUserCurrent->getId());
            if ($aFriends['count']) {
                $aFilter['album_type']['friend'] = array_keys($aFriends['collection']);
            }
        }
        return $this->GetImagesByFilter($aFilter, $iPage, $iPerPage);
    }

    /**
     * Get random images
     * @param int $iLimit
     * @return \PluginLsgallery_ModuleImage_EntityImage
     */
    public function GetRandomImages($iLimit = 5)
    {
        $aImagesId = $this->oMapper->GetRandomImages($iLimit);

        return $this->GetImagesAdditionalData($aImagesId);
    }

    /**
     * Get tags for open images
     *
     * @param int $iLimit
     * @return \PluginLsgallery_ModuleImage_EntityImageTag
     */
    public function GetOpenImageTags($iLimit)
    {
        if (false === ($data = $this->Cache_Get("image_tag_{$iLimit}_open"))) {
            $data = $this->oMapper->GetOpenImageTags($iLimit);
            $this->Cache_Set($data, "image_tag_{$iLimit}_open", array('image_update'), 60 * 60 * 24 * 3);
        }
        return $data;
    }

    /**
     * Get images by tag
     * @param string $sTag
     * @param int $iPage
     * @param int $iPerPage
     * @return \PluginLsgallery_ModuleImage_EntityImage
     */
    public function GetImagesByTag($sTag, $iPage, $iPerPage)
    {
        if (false === ($data = $this->Cache_Get("image_tag_{$sTag}_{$iPage}_{$iPerPage}"))) {
            $data = array(
                'collection' => $this->oMapper->GetImagesByTag($sTag, $iCount, $iPage, $iPerPage),
                'count' => $iCount)
            ;
            $this->Cache_Set($data, "image_tag_{$sTag}_{$iPage}_{$iPerPage}", array('image_update'), 60 * 60 * 24 * 2);
        }
        $data['collection'] = $this->GetImagesAdditionalData($data['collection']);
        return $data;
    }

    /**
     * Get favourite images by user
     * @param string $sUserId
     * @param int $iCurrPage
     * @param int $iPerPage
     * @return \PluginLsgallery_ModuleImage_EntityImage
     */
    public function GetImagesFavouriteByUserId($sUserId, $iCurrPage, $iPerPage)
    {

        $data = ($this->oUserCurrent && $sUserId == $this->oUserCurrent->getId()) ?
                $this->Favourite_GetFavouritesByUserId($sUserId, 'image', $iCurrPage, $iPerPage) :
                $this->GetFavouriteOpenImagesByUserId($sUserId, $iCurrPage, $iPerPage);
        $data['collection'] = $this->GetImagesAdditionalData($data['collection']);
        return $data;
    }

    /**
     * Get favourite open images by user
     *
     * @param string $sUserId
     * @param int $iCurrPage
     * @param int $iPerPage
     * @return \PluginLsgallery_ModuleImage_EntityImage
     */
    public function GetFavouriteOpenImagesByUserId($sUserId, $iCurrPage, $iPerPage)
    {
        if (false === ($data = $this->Cache_Get("image_favourite_user_{$sUserId}_{$iCurrPage}_{$iPerPage}_open"))) {
            $data = array(
                'collection' => $this->oMapper->GetFavouriteOpenImagesByUserId($sUserId, $iCount, $iCurrPage, $iPerPage),
                'count' => $iCount
            );
            $this->Cache_Set(
                    $data, "images_favourite_user_{$sUserId}_{$iCurrPage}_{$iPerPage}_open", array(
                "favourite_image_change",
                "favourite_image_change_user_{$sUserId}"
                    ), 60 * 60 * 24 * 1
            );
        }
        return $data;
    }

    /**
     * Get count fav images by user
     *
     * @param string $sUserId
     * @return int
     */
    public function GetCountImagesFavouriteByUserId($sUserId)
    {
        return ($this->oUserCurrent && $sUserId == $this->oUserCurrent->getId()) ? $this->Favourite_GetCountFavouritesByUserId($sUserId, 'image') : $this->GetCountFavouriteOpenImagesByUserId($sUserId);
    }

    /**
     * Get count open fav images by user
     *
     * @param string $sUserId
     * @return int
     */
    public function GetCountFavouriteOpenImagesByUserId($sUserId)
    {
        if (false === ($data = $this->Cache_Get("image_count_favourite_user_{$sUserId}_open"))) {
            $data = $this->oMapper->GetCountFavouriteOpenImagesByUserId($sUserId);
            $this->Cache_Set(
                    $data, "image_count_favourite_user_{$sUserId}_open", array(
                "favourite_image_change",
                "favourite_image_change_user_{$sUserId}"
                    ), 60 * 60 * 24 * 1
            );
        }
        return $data;
    }

    /**
     * Add image user
     *
     * @param PluginLsgallery_ModuleImage_EntityImageUser $oImageUser
     * @return PluginLsgallery_ModuleImage_EntityImageUser|boolean
     */
    public function AddImageUser($oImageUser)
    {
        if ($this->oMapper->AddImageUser($oImageUser) !== false) {
            $this->Cache_Delete("image_users_{$oImageUser->getImageId()}");
            $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("image_mark_user_{$oImageUser->getTargetUserId()}"));
            return $oImageUser;
        }
        return false;
    }

    /**
     * Get image user
     *
     * @param type $sUserId
     * @param type $sImageId
     * @return type
     */
    public function GetImageUser($sUserId, $sImageId)
    {
        return $this->oMapper->GetImageUser($sUserId, $sImageId);
    }

    /**
     * Get image user by image id
     *
     * @param string $sImageId
     * @return \PluginLsgallery_ModuleImage_EntityImageUser
     */
    public function GetImageUsersByImageId($sImageId)
    {
        if (false === ($data = $this->Cache_Get("image_users_{$sImageId}"))) {
            $data = $this->oMapper->GetImageUsersByImageId($sImageId);
            $this->Cache_Set(
                    $data, "image_users_{$sImageId}", array(), 60 * 60 * 24 * 1
            );
        }
        return $data;
    }

    /**
     * Change image user status
     *
     * @param PluginLsgallery_ModuleImage_EntityImageUser $oImageUser
     * @return boolean
     */
    public function ChangeStatusImageUser($oImageUser)
    {
        $this->Cache_Delete("image_users_{$oImageUser->getImageId()}");
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("image_mark_user_{$oImageUser->getTargetUserId()}"));
        return $this->oMapper->ChangeStatusImageUser($oImageUser);
    }

    /**
     * Delete image user
     *
     * @param PluginLsgallery_ModuleImage_EntityImageUser $oImageUser
     * @return boolean
     */
    public function DeleteImageUser($oImageUser)
    {
        $this->Cache_Delete("image_users_{$oImageUser->getImageId()}");
        return $this->oMapper->DeleteImageUser($oImageUser);
    }

    /**
     * Get images by user marked per page
     *
     * @param string $sUserId
     * @param int $iCurrPage
     * @param int $iPerPage
     * @return PluginLsgallery_ModuleImage_EntityImage
     */
    public function GetImagesByUserMarked($sUserId, $iCurrPage, $iPerPage)
    {
        if (false === ($data = $this->Cache_Get("image_marked_user_{$sUserId}_{$iCurrPage}_{$iPerPage}"))) {
            $data = array(
                'collection' => $this->oMapper->GetImagesByUserMarked($sUserId, $iCount, $iCurrPage, $iPerPage),
                'count' => $iCount
            );
            $this->Cache_Set(
                    $data, "image_marked_user_{$sUserId}_{$iCurrPage}_{$iPerPage}", array(
                "image_mark_user_{$sUserId}"
                    ), 60 * 60 * 24 * 1
            );
        }
        $data['collection'] = $this->GetImagesAdditionalData($data['collection']);
        return $data;
    }

    /**
     * Get prev image id
     *
     * @param PluginLsgallery_ModuleImage_EntityImage$oImage
     *
     * @return PluginLsgallery_ModuleImage_EntityImage|null
     */
    public function GetPrevImage($oImage)
    {
        if (false === ($sId = $this->Cache_Get("image_prev_{$oImage->getId()}"))) {
            $sId = $this->oMapper->GetPrevImageId($oImage);
            $this->Cache_Set($sId, "image_prev_{$oImage->getId()}", array('image_update', 'image_new'), 60 * 60 * 24 * 1);
        }
        return $this->GetImageById($sId);
    }

    /**
     * Get next image id
     *
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     *
     * @return PluginLsgallery_ModuleImage_EntityImage|null
     */
    public function GetNextImage($oImage)
    {
        if (false === ($sId = $this->Cache_Get("image_next_{$oImage->getId()}"))) {
            $sId = $this->oMapper->GetNextImageId($oImage);
            $this->Cache_Set($sId, "image_next_{$oImage->getId()}", array('image_update', 'image_new'), 60 * 60 * 24 * 1);
        }
        return $this->GetImageById($sId);
    }

    /**
     * Move image from one album to other
     *
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     * @param PluginLsgallery_ModuleAlbum_EntityAlbum $oAlbumFrom
     * @param PluginLsgallery_ModuleAlbum_EntityAlbum $oAlbumTo
     *
     * @return boolean
     */

    public function MoveImage($oImage, $oAlbumFrom, $oAlbumTo)
    {
        $oImage->setAlbumId($oAlbumTo->getId());
        $this->UpdateImage($oImage);

        $this->Comment_MoveTargetParent($oAlbumFrom->getId(), 'image', $oAlbumTo->getId());

        if ($oAlbumFrom->getCoverId() == $oImage->getId()) {
            $oAlbumFrom->setCoverId(null);
        }

        $oAlbumFrom->setImageCount($oAlbumFrom->getImageCount() - 1);
        $this->PluginLsgallery_Album_UpdateAlbum($oAlbumFrom);

        $oAlbumTo->setImageCount($oAlbumTo->getImageCount() + 1);
        $this->PluginLsgallery_Album_UpdateAlbum($oAlbumTo);
    }

    /**
     * Пересчитывает счетчики голосований
     *
     * @return bool
     */
    public function RecalculateVote()
    {
        return $this->oMapper->RecalculateVote();
    }

    /**
     * Пересчитывает счетчик избранных топиков
     *
     * @return bool
     */
    public function RecalculateFavourite()
    {
        return $this->oMapper->RecalculateFavourite();
    }

    /**
     * Check if resize exist
     *
     * @param $sOriginalPath
     * @param $sWidth
     *
     * @return void
     */
    public function checkImageExist($sOriginalPath, $sWidth)
    {
        $aPathInfo = pathinfo($sOriginalPath);
        $sFilePath = $aPathInfo['dirname'] . '/' . $aPathInfo['filename'] . '_' . $sWidth . '.' . $aPathInfo['extension'];
        if (file_exists(Config::Get('path.root.server') . $sFilePath)) {
            return;
        }

        $sPath = $aPathInfo['dirname'] . '/';
        $sFileName = $aPathInfo['filename'];
        $sFile = Config::Get('path.root.server') . $sOriginalPath;
        $aSizes = Config::Get('plugin.lsgallery.size');
        $aParams = $this->Image_BuildParams('lsgallery');

        preg_match('/([0-9]+)(crop)?/', $sWidth, $aCurSize);

        foreach ($aSizes as $aSize) {
            if ($aSize['w'] == $aCurSize[1] && $aSize['crop'] == isset($aCurSize[2])) {
                // Для каждого указанного в конфиге размера генерируем картинку
                $sNewFileName = $sFileName . '_' . $aSize['w'];
                $oImage = new LiveImage($sFile);
                if ($aSize['crop']) {
                    $this->Image_CropProportion($oImage, $aSize['w'], $aSize['h'], true);
                    $sNewFileName .= 'crop';
                }
                $this->Image_Resize($sFile, $sPath, $sNewFileName, Config::Get('view.img_max_width'), Config::Get('view.img_max_height'), $aSize['w'], $aSize['h'], true, $aParams, $oImage);
            }
        }

        return;
    }
}