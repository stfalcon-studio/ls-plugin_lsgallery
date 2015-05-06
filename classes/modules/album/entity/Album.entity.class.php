<?php

/**
 * Class PluginLsgallery_ModuleAlbum_EntityAlbum
 *
 * @method ModuleUser_EntityUser getUser
 * @method PluginLsgallery_ModuleImage_EntityImage getCover
 */
class PluginLsgallery_ModuleAlbum_EntityAlbum extends Entity
{
    /**
     * Type open
     */
    const TYPE_OPEN = 'open';

    /**
     * Type friend
     */
    const TYPE_FRIEND = 'friend';

    /**
     * Type personal
     */
    const TYPE_PERSONAL = 'personal';

    /**
     * Type shared
     */
    const TYPE_SHARED = 'shared';

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_aData['album_id'];
    }

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->_aData['album_user_id'];
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_aData['album_title'];
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_aData['album_description'];
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_aData['album_type'];
    }

    /**
     * Get date add
     *
     * @return datetime
     */
    public function getDateAdd()
    {
        return $this->_aData['album_date_add'];
    }

    /**
     * @return datetime
     */
    public function getDateEdit()
    {
        return $this->_aData['album_date_edit'];
    }

    /**
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
     * @return int
     */
    public function getCoverId()
    {
        return $this->_aData['album_cover_image_id'];
    }

    /**
     * @return int
     */
    public function getImageCount()
    {
        return $this->_aData['image_count'];
    }

    /**
     * Get localized types
     *
     * @param \ModuleLang $engine
     *
     * @return array
     */
    public static function getLocalizedTypes($engine)
    {
        return array(
            self::TYPE_OPEN     => $engine->Lang_Get('plugin.lsgallery.lsgallery_type_open'),
            self::TYPE_FRIEND   => $engine->Lang_Get('plugin.lsgallery.lsgallery_type_friend'),
            self::TYPE_PERSONAL => $engine->Lang_Get('plugin.lsgallery.lsgallery_type_personal'),
            self::TYPE_SHARED   => $engine->Lang_Get('plugin.lsgallery.lsgallery_type_shared'),
        );
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
                $sPath = Router::GetPath('gallery') . 'album/' . $this->getId();
                break;
            case 'edit':
                $sPath = Router::GetPath('gallery') . 'update/' . $this->getId();
                break;
            case 'delete':
                $sPath = Router::GetPath('gallery') . 'delete/' . $this->getId();
                break;
            case 'images':
                $sPath = Router::GetPath('gallery') . 'admin-images/' . $this->getId();
                break;
            default:
                $sPath = Router::GetPath('gallery') . 'aldbum/' . $this->getId();
                break;
        }

        return $sPath;
    }

    /**
     * Get default cover
     *
     * @return string
     */
    public function getDefaultCover()
    {
        return Plugin::GetTemplateWebPath('lsgallery') . 'images/default-cover.png';
    }

    /**
     * Set id
     *
     * @param int $data Album id
     */
    public function setId($data)
    {
        $this->_aData['album_id'] = $data;
    }

    /**
     * Set user id
     *
     * @param int $data Album user id
     */
    public function setUserId($data)
    {
        $this->_aData['album_user_id'] = $data;
    }

    /**
     * Set title
     *
     * @param string $data Album title
     */
    public function setTitle($data)
    {
        $this->_aData['album_title'] = $data;
    }

    /**
     * Set description
     *
     * @param string $data Album description
     */
    public function setDescription($data)
    {
        $this->_aData['album_description'] = $data;
    }

    /**
     * Set type
     *
     * @param string $data Album type
     */
    public function setType($data)
    {
        $this->_aData['album_type'] = $data;
    }

    /**
     * Set date add
     *
     * @param datetime $data Album date add
     */
    public function setDateAdd($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H::i:s');
        }
        $this->_aData['album_date_add'] = $data;
    }

    /**
     * Set date edit
     *
     * @param datetime $data Album date edit
     */
    public function setDateEdit($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H:i:s');
        }
        $this->_aData['album_date_edit'] = $data;
    }

    /**
     * Set cover id
     *
     * @param int $data Album cover image id
     */
    public function setCoverId($data)
    {
        $this->_aData['album_cover_image_id'] = $data;
    }

    /**
     * Set image count
     *
     * @param int $data Image count
     */
    public function setImageCount($data)
    {
        $this->_aData['image_count'] = $data;
    }

    /**
     * Is public
     *
     * @return bool
     */
    public function isPublic()
    {
        switch ($this->getType()) {
            case self::TYPE_SHARED:
                return true;
            case self::TYPE_OPEN:
                return true;
            default:
                return false;
        }
    }
}
