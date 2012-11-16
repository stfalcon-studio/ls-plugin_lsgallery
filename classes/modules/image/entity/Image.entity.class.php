<?php

/**
 * Image entity
 *
 * @method ModuleUser_EntityUser getUser
 * @method boolean getIsFavourite
 * @method ModuleVote_EntityVote getVote
 */
class PluginLsgallery_ModuleImage_EntityImage extends Entity
{

    public function getId()
    {
        return $this->_aData['image_id'];
    }

    public function getUserId()
    {
        return $this->_aData['user_id'];
    }

    public function getAlbumId()
    {
        return $this->_aData['album_id'];
    }

    public function getDescription()
    {
        return $this->_aData['image_description'];
    }

    public function getImageTags()
    {
        return $this->_aData['image_tags'];
    }

    public function getTagsArray()
    {
        if ($this->getImageTags()) {
            return explode(',', $this->getImageTags());
        }
        return null;
    }

    public function getFilename()
    {
        return $this->_aData['image_filename'];
    }

    public function getDateAdd()
    {
        return $this->_aData['image_date_add'];
    }

    public function getDateEdit()
    {
        return $this->_aData['image_date_edit'];
    }

    public function getDateModified(){
        if($this->getDateEdit() != null){
            return $this->getDateEdit();
        } else {
            return $this->getDateAdd();
        }
    }
    public function getCountComment()
    {
        return $this->_aData['image_count_comment'];
    }

    public function getRating()
    {
        return number_format(round($this->_aData['image_rating'], 2), 0, '.', '');
    }

    /**
     * Возвращает число проголосовавших за топик
     *
     * @return int|null
     */
    public function getCountVote() {
        return $this->_getDataOne('image_count_vote');
    }
    /**
     * Возвращает число проголосовавших за топик положительно
     *
     * @return int|null
     */
    public function getCountVoteUp() {
        return $this->_getDataOne('image_count_vote_up');
    }
    /**
     * Возвращает число проголосовавших за топик отрицательно
     *
     * @return int|null
     */
    public function getCountVoteDown() {
        return $this->_getDataOne('image_count_vote_down');
    }
    /**
     * Возвращает число воздержавшихся при голосовании за топик
     *
     * @return int|null
     */
    public function getCountVoteAbstain() {
        return $this->_getDataOne('image_count_vote_abstain');
    }

    public function getCountFavourite()
    {
        return $this->_aData['image_count_favourite'];
    }

    public function getWebPath($sWidth = null)
    {
        if ($this->getFilename()) {
            if ($sWidth) {
                $aPathInfo = pathinfo($this->getFilename());
                $sFilePath = $aPathInfo['dirname'] . '/' . $aPathInfo['filename'] . '_' . $sWidth . '.' . $aPathInfo['extension'];
                $this->PluginLsgallery_Image_CheckImageExist($this->getFilename(), $sWidth);

                return Config::Get('path.root.web') . $sFilePath;
            } else {
                return Config::Get('path.root.web') . $this->getFilename();
            }
        } else {
            return null;
        }
    }

    public function getAlbum()
    {
        if (!isset($this->_aData['album'])) {
            $this->_aData['album'] = $this->PluginLsgallery_Album_GetAlbumById($this->getAlbumId());
        }

        return $this->_aData['album'];
    }

    public function getUrlFull($type = 'view')
    {
        switch ($type) {
            case 'view':
                $sPath = Router::GetPath('gallery') . 'image/' . $this->getId();
                break;
            default:
                $sPath = Router::GetPath('gallery') . 'image/' . $this->getId();
                break;
        }

        return $sPath;
    }

    /**
     * @param string $sKey
     * @return mixed|null
     */
    public function _getDataOne($sKey) {
        if(array_key_exists($sKey,$this->_aData)) {
            return $this->_aData[$sKey];
        }
        return null;
    }

    public function setId($data)
    {
        $this->_aData['image_id'] = $data;
    }

    public function setUserId($data)
    {
        $this->_aData['user_id'] = $data;
    }

    public function setAlbumId($data)
    {
        $this->_aData['album_id'] = $data;
    }

    public function setDescription($data)
    {
        $this->_aData['image_description'] = $data;
    }

    public function setImageTags($data)
    {
        $this->_aData['image_tags'] = $data;
    }

    public function setFilename($data)
    {
        $this->_aData['image_filename'] = $data;
    }

    public function setDateAdd($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H::i:s');
        }
        $this->_aData['image_date_add'] = $data;
    }

    public function setDateEdit($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H::i:s');
        }
        $this->_aData['image_date_edit'] = $data;
    }

    public function setCountComment($data)
    {
        $this->_aData['image_count_comment'] = $data;
    }

    public function setRating($data)
    {
        $this->_aData['image_rating'] = $data;
    }

    public function setCountVote($data)
    {
        $this->_aData['image_count_vote'] = $data;
    }

    public function setCountFavourite($data)
    {
        $this->_aData['image_count_favourite'] = $data;
    }

    /**
     * Устанавливает количество проголосовавших в плюс
     *
     * @param int $data
     */
    public function setCountVoteUp($data)
    {
        $this->_aData['image_count_vote_up'] = $data;
    }

    /**
     * Устанавливает количество проголосовавших в минус
     *
     * @param int $data
     */
    public function setCountVoteDown($data)
    {
        $this->_aData['image_count_vote_down'] = $data;
    }

    /**
     * Устанавливает число воздержавшихся
     *
     * @param int $data
     */
    public function setCountVoteAbstain($data)
    {
        $this->_aData['image_count_vote_abstain'] = $data;
    }

}