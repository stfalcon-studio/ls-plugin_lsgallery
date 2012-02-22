<?php

class PluginLsgallery_ModuleAlbum extends Module
{

    /**
     *
     * @var PluginLsgallery_ModuleAlbum_MapperAlbum
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
     * Create album
     * 
     * @param PluginLsgallery_ModuleAlbum_EntityAlbum $oAlbum
     * @return boolean|PluginLsgallery_ModuleAlbum_EntityAlbum 
     */
    public function CreateAlbum($oAlbum)
    {
        $oAlbum->setDateAdd();
        if ($sId = $this->oMapper->CreateAlbum($oAlbum)) {
            $oAlbum->setId($sId);
            $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('album_new'));
            return $oAlbum;
        }
        return false;
    }

    /**
     * Update album
     * 
     * @param PluginLsgallery_ModuleAlbum_EntityAlbum $oAlbum
     * @return boolean 
     */
    public function UpdateAlbum($oAlbum)
    {
        $oAlbum->setDateEdit();
        if ($this->oMapper->UpdateAlbum($oAlbum)) {
            //чистим зависимые кеши
            $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('album_update', "album_update_{$oAlbum->getId()}", "image_update"));
            $this->Cache_Delete("album_{$oAlbum->getId()}");
            return true;
        }
        return false;
    }

    /**
     * Delete album
     * @param int|PluginLsgallery_ModuleAlbum_EntityAlbum $iAlbumId
     * @return boolean 
     */
    public function DeleteAlbum($iAlbumId)
    {
        if ($iAlbumId instanceof PluginLsgallery_ModuleAlbum_EntityAlbum) {
            $iAlbumId = $iAlbumId->getId();
        }
        if (!$this->oMapper->DeleteAlbum($iAlbumId)) {
            return false;
        }
        /**
         * Чистим кеш
         */
        $this->Cache_Clean(
                Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(
            "album_update", "image_update", "vote_update", "comment_update"
                )
        );

        //@todo удаление комментариев и голосов к картинкам
        $this->Cache_Delete("album_{$iAlbumId}");


        return true;
    }

    /**
     * Get album by Id
     *
     * @param string $sAlbumId
     * @return PluginLsgallery_ModuleAlbum_EntityAlbum|null
     */
    public function GetAlbumById($sAlbumId)
    {
        $aAlbums = $this->GetAlbumsAdditionalData($sAlbumId);
        if (isset($aAlbums[$sAlbumId])) {
            return $aAlbums[$sAlbumId];
        }
        return null;
    }

    /**
     * Get albums additional data
     * 
     * @todo Подтягивание картинок
     * @param array $aAlbumsId
     * @param array $aAllowData
     * @return array 
     */
    public function GetAlbumsAdditionalData($aAlbumsId, $aAllowData = array('user' => array(), 'cover' => array()))
    {
        func_array_simpleflip($aAllowData);
        if (!is_array($aAlbumsId)) {
            $aAlbumsId = array($aAlbumsId);
        }

        $aAlbums = $this->GetAlbumsByArrayId($aAlbumsId);

        $aUserId = array();
        $aImageId = array();

        foreach ($aAlbums as $oAlbum) {
            /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
            if (isset($aAllowData['user'])) {
                $aUserId[] = $oAlbum->getUserId();
            }
            if (isset($aAllowData['cover'])) {
                $aImageId[] = $oAlbum->getCoverId();
            }
        }
        $aUsers = isset($aAllowData['user']) && is_array($aAllowData['user']) ? $this->User_GetUsersAdditionalData($aUserId, $aAllowData['user']) : $this->User_GetUsersAdditionalData($aUserId);
        $aImages = isset($aAllowData['cover']) && is_array($aAllowData['cover']) ? $this->PluginLsgallery_Image_GetImagesAdditionalData($aImageId, $aAllowData['cover']) : $this->PluginLsgallery_Image_GetImagesAdditionalData($aUserId);
        foreach ($aAlbums as $oAlbum) {
            if (isset($aUsers[$oAlbum->getUserId()])) {
                $oAlbum->setUser($aUsers[$oAlbum->getUserId()]);
            } else {
                $oAlbum->setUser(null);
            }

            if (isset($aImages[$oAlbum->getCoverId()])) {
                $oAlbum->setCover($aImages[$oAlbum->getCoverId()]);
            } else {
                $oAlbum->setCover(null);
            }
        }

        return $aAlbums;
    }

    /**
     * Get albums by array Id
     * 
     * @param array $aAlbumId
     * @return array 
     */
    public function GetAlbumsByArrayId($aAlbumId)
    {
        if (!$aAlbumId) {
            return array();
        }

        if (Config::Get('sys.cache.solid')) {
            return $this->GetAlbumsByArrayIdSolid($aAlbumId);
        }

        if (!is_array($aAlbumId)) {
            $aAlbumId = array($aAlbumId);
        }

        $aAlbumId = array_unique($aAlbumId);
        $aAlbums = array();
        $aAlbumIdNotNeedQuery = array();
        /**
         * Делаем мульти-запрос к кешу
         */
        $aCacheKeys = func_build_cache_keys($aAlbumId, 'album_');
        if (false !== ($data = $this->Cache_Get($aCacheKeys))) {
            /**
             * проверяем что досталось из кеша
             */
            foreach ($aCacheKeys as $sValue => $sKey) {
                if (array_key_exists($sKey, $data)) {
                    if ($data[$sKey]) {
                        $aAlbums[$data[$sKey]->getId()] = $data[$sKey];
                    } else {
                        $aAlbumIdNotNeedQuery[] = $sValue;
                    }
                }
            }
        }

        $aAlbumIdNeedQuery = array_diff($aAlbumId, array_keys($aAlbums));
        $aAlbumIdNeedQuery = array_diff($aAlbumIdNeedQuery, $aAlbumIdNotNeedQuery);
        $aAlbumIdNeedStore = $aAlbumIdNeedQuery;
        if ($data = $this->oMapper->GetAlbumsByArrayId($aAlbumIdNeedQuery)) {
            foreach ($data as $oAlbum) {
                /**
                 * Добавляем к результату и сохраняем в кеш
                 */
                $aAlbums[$oAlbum->getId()] = $oAlbum;
                $this->Cache_Set($oAlbum, "album_{$oAlbum->getId()}", array(), 60 * 60 * 24 * 4);
                $aAlbumIdNeedStore = array_diff($aAlbumIdNeedStore, array($oAlbum->getId()));
            }
        }
        /**
         * Сохраняем в кеш запросы не вернувшие результата
         */
        foreach ($aAlbumIdNeedStore as $sId) {
            $this->Cache_Set(null, "album_{$sId}", array(), 60 * 60 * 24 * 4);
        }
        /**
         * Сортируем результат согласно входящему массиву
         */
        $aAlbums = func_array_sort_by_keys($aAlbums, $aAlbumId);
        return $aAlbums;
    }

    /**
     * Get albums by array id from solid cache
     * 
     * @param array $aAlbumId
     * @return array 
     */
    public function GetAlbumsByArrayIdSolid($aAlbumId)
    {
        if (!is_array($aAlbumId)) {
            $aAlbumId = array($aAlbumId);
        }
        $aAlbumId = array_unique($aAlbumId);
        $aAlbums = array();
        $s = join(',', $aAlbumId);
        if (false === ($data = $this->Cache_Get("album_id_{$s}"))) {
            $data = $this->oMapper->GetAlbumsByArrayId($aAlbumId);
            foreach ($data as $oAlbum) {
                $aAlbums[$oAlbum->getId()] = $oAlbum;
            }
            $this->Cache_Set($aAlbums, "album_id_{$s}", array("album_update"), 60 * 60 * 24 * 1);
            return $aAlbums;
        }
        return $data;
    }

    public function GetAlbumsIndex($iPage, $iPerPage, $bMy = false)
    {
        if ($bMy && $this->oUserCurrent) {
            $aFilter['user_id'] = $this->oUserCurrent->getId();
        } else {
            $aFilter = array(
                'album_type' => array(
                    'open' => true
                ),
                'not_empty' => true
            );

            if ($this->oUserCurrent) {
                $aFriends = $this->User_GetUsersFriend($this->oUserCurrent->getId());
                if (count($aFriends)) {
                    $aFilter['album_type']['friend'] = array_keys($aFriends);
                }
            }
        }
        return $this->GetAlbumsByFilter($aFilter, $iPage, $iPerPage);
    }

    public function GetAlbumsByFilter($aFilter, $iPage = 0, $iPerPage = 0, $aAllowData = array('user' => array(), 'cover' => array()), $bOnlyIds = false)
    {
        $s = serialize($aFilter);
        if (false === ($data = $this->Cache_Get("album_filter_{$s}_{$iPage}_{$iPerPage}"))) {
            $data = ($iPage * $iPerPage != 0) ? array(
                'collection' => $this->oMapper->GetAlbums($aFilter, $iCount, $iPage, $iPerPage),
                'count' => $iCount
                    ) : array(
                'collection' => $this->oMapper->GetAllAlbums($aFilter),
                'count' => $this->GetCountAlbumsByFilter($aFilter)
                    );
            $this->Cache_Set($data, "album_filter_{$s}_{$iPage}_{$iPerPage}", array('album_update', 'album_new'), 60 * 60 * 24 * 3);
        }
        if (!$bOnlyIds) {
            $data['collection'] = $this->GetAlbumsAdditionalData($data['collection'], $aAllowData);
        }
        return $data;
    }

    public function GetCountAlbumsByFilter($aFilter)
    {
        $s = serialize($aFilter);
        if (false === ($data = $this->Cache_Get("album_count_{$s}"))) {
            $data = $this->oMapper->GetCountAlbums($aFilter);
            $this->Cache_Set($data, "album_count_{$s}", array('album_update', 'album_new'), 60 * 60 * 24 * 1);
        }
        return $data;
    }
    
    public function GetAlbumsPersonalByUser($sUserId,$iPage,$iPerPage) {
        
        $aFilter = array(
            'user_id' => $sUserId,
            'album_type' => array(
                'open',
            ),
            
        );

        if ($this->oUserCurrent) {
            $aFriends = $this->User_GetUsersFriend($this->oUserCurrent->getId());
            if (count($aFriends)) {
                $aFilter['album_type']['friend'] = array_keys($aFriends);
            }
        }
        return $this->GetAlbumsByFilter($aFilter, $iPage, $iPerPage);
        
	}

}