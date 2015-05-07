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
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_aData['image_id'];
    }

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->_aData['user_id'];
    }

    /**
     * Get album id
     *
     * @return int
     */
    public function getAlbumId()
    {
        return $this->_aData['album_id'];
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_aData['image_description'];
    }

    /**
     * Get image tags
     *
     * @return string
     */
    public function getImageTags()
    {
        return $this->_aData['image_tags'];
    }

    /**
     * Get tags array
     *
     * @return array|null
     */
    public function getTagsArray()
    {
        if ($this->getImageTags()) {
            return explode(',', $this->getImageTags());
        }

        return null;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_aData['image_filename'];
    }

    /**
     * Get date add
     *
     * @return datetime
     */
    public function getDateAdd()
    {
        return $this->_aData['image_date_add'];
    }

    /**
     * Get date edit
     *
     * @return datetime
     */
    public function getDateEdit()
    {
        return $this->_aData['image_date_edit'];
    }

    /**
     * Get date modified
     *
     * @return datetime
     */
    public function getDateModified()
    {
        if ($this->getDateEdit() != null) {
            return $this->getDateEdit();
        } else {
            return $this->getDateAdd();
        }
    }

    /**
     * Get count comment
     *
     * @return int
     */
    public function getCountComment()
    {
        return $this->_aData['image_count_comment'];
    }

    /**
     * Get rating
     *
     * @return string
     */
    public function getRating()
    {
        return number_format(round($this->_aData['image_rating'], 2), 0, '.', '');
    }

    /**
     * Возвращает число проголосовавших за топик
     *
     * @return int
     */
    public function getCountVote()
    {
        return $this->_getDataOne('image_count_vote');
    }

    /**
     * Возвращает число проголосовавших за топик положительно
     *
     * @return int
     */
    public function getCountVoteUp()
    {
        return $this->_getDataOne('image_count_vote_up');
    }

    /**
     * Возвращает число проголосовавших за топик отрицательно
     *
     * @return int
     */
    public function getCountVoteDown()
    {
        return $this->_getDataOne('image_count_vote_down');
    }

    /**
     * Возвращает число воздержавшихся при голосовании за топик
     *
     * @return int
     */
    public function getCountVoteAbstain()
    {
        return $this->_getDataOne('image_count_vote_abstain');
    }

    /**
     * Get count favourite
     *
     * @return int
     */
    public function getCountFavourite()
    {
        return $this->_aData['image_count_favourite'];
    }

    /**
     * Get web path
     *
     * @param null $sWidth
     *
     * @return null|string
     */
    public function getWebPath($sWidth = null)
    {
        if ($this->getFilename()) {
            if ($sWidth) {
                $aPathInfo = pathinfo($this->getFilename());
                $sFilePath = $aPathInfo['dirname'] . '/' . $aPathInfo['filename'] . '_' . $sWidth . '.'
                             . $aPathInfo['extension'];
                $this->PluginLsgallery_Image_CheckImageExist($this->getFilename(), $sWidth);

                return Config::Get('path.root.web') . $sFilePath;
            } else {
                return Config::Get('path.root.web') . $this->getFilename();
            }
        } else {
            return null;
        }
    }

    /**
     * Get album
     *
     * @return \PluginLsgallery_ModuleAlbum_EntityAlbum
     */
    public function getAlbum()
    {
        if (!isset($this->_aData['album'])) {
            $this->_aData['album'] = $this->PluginLsgallery_Album_GetAlbumById($this->getAlbumId());
        }

        return $this->_aData['album'];
    }

    /**
     * Get full url
     *
     * @param string $type
     *
     * @return string
     */
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
     * Get data one
     *
     * @param string $sKey
     *
     * @return mixed|null
     */
    public function _getDataOne($sKey)
    {
        if (array_key_exists($sKey, $this->_aData)) {
            return $this->_aData[$sKey];
        }

        return null;
    }

    /**
     * Set id
     *
     * @param int $data Image id
     */
    public function setId($data)
    {
        $this->_aData['image_id'] = $data;
    }

    /**
     * Set user id
     *
     * @param int $data User id
     */
    public function setUserId($data)
    {
        $this->_aData['user_id'] = $data;
    }

    /**
     * Set album id
     *
     * @param int $data Album id
     */
    public function setAlbumId($data)
    {
        $this->_aData['album_id'] = $data;
    }

    /**
     * Set album
     *
     * @param \PluginLsgallery_ModuleAlbum_EntityAlbum $data Album
     */
    public function setAlbum($data)
    {
        $this->_aData['album'] = $data;
    }

    /**
     * @param string $data Image description
     */
    public function setDescription($data)
    {
        $this->_aData['image_description'] = $data;
    }

    /**
     * Set image tags
     *
     * @param string $data Image tags
     */
    public function setImageTags($data)
    {
        $this->_aData['image_tags'] = $data;
    }

    /**
     * Set file name
     *
     * @param string $data Image filename
     */
    public function setFilename($data)
    {
        $this->_aData['image_filename'] = $data;
    }

    /**
     * Set date add
     *
     * @param datetime $data Image date add
     */
    public function setDateAdd($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H::i:s');
        }
        $this->_aData['image_date_add'] = $data;
    }

    /**
     * Set date edit
     *
     * @param datetime $data Image date edit
     */
    public function setDateEdit($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H::i:s');
        }
        $this->_aData['image_date_edit'] = $data;
    }

    /**
     * Set count comment
     *
     * @param int $data Image count comment
     */
    public function setCountComment($data)
    {
        $this->_aData['image_count_comment'] = $data;
    }

    /**
     * Set rating
     *
     * @param float $data Image rating
     */
    public function setRating($data)
    {
        $this->_aData['image_rating'] = $data;
    }

    /**
     * Set count vote
     *
     * @param int $data Image count vote
     */
    public function setCountVote($data)
    {
        $this->_aData['image_count_vote'] = $data;
    }

    /**
     * Set count favorite
     *
     * @param int $data Image count favourite
     */
    public function setCountFavourite($data)
    {
        $this->_aData['image_count_favourite'] = $data;
    }

    /**
     * Устанавливает количество проголосовавших в плюс
     *
     * @param int $data Image count vote up
     */
    public function setCountVoteUp($data)
    {
        $this->_aData['image_count_vote_up'] = $data;
    }

    /**
     * Устанавливает количество проголосовавших в минус
     *
     * @param int $data Image count vote down
     */
    public function setCountVoteDown($data)
    {
        $this->_aData['image_count_vote_down'] = $data;
    }

    /**
     * Устанавливает число воздержавшихся
     *
     * @param int $data Image count vote abstain
     */
    public function setCountVoteAbstain($data)
    {
        $this->_aData['image_count_vote_abstain'] = $data;
    }
}
