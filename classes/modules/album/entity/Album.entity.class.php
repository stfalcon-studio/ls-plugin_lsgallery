<?php

/**
 * Album entity
 * 
 * @method ModuleUser_EntityUser getUser 
 * @method PluginLsgallery_ModuleImage_EntityImage getCover 
 */
class PluginLsgallery_ModuleAlbum_EntityAlbum extends Entity
{

    const TYPE_OPEN = 'open';
    const TYPE_FRIEND = 'friend';
    const TYPE_PERSONAL = 'personal';

    public function getId()
    {
        return $this->_aData['album_id'];
    }

    public function getUserId()
    {
        return $this->_aData['album_user_id'];
    }

    public function getTitle()
    {
        return $this->_aData['album_title'];
    }

    public function getDescription()
    {
        return $this->_aData['album_description'];
    }

    public function getType()
    {
        return $this->_aData['album_type'];
    }

    public function getDateAdd()
    {
        return $this->_aData['album_date_add'];
    }

    public function getDateEdit()
    {
        return $this->_aData['album_date_edit'];
    }

    public function getCoverId()
    {
        return $this->_aData['album_cover_image_id'];
    }

    public function getImageCount()
    {
        return $this->_aData['image_count'];
    }

    public static function getLocalizedTypes($engine)
    {
        return array(
            self::TYPE_OPEN => $engine->Lang_Get('plugin.lsgallery.lsgallery_type_open'),
            self::TYPE_FRIEND => $engine->Lang_Get('plugin.lsgallery.lsgallery_type_friend'),
            self::TYPE_PERSONAL => $engine->Lang_Get('plugin.lsgallery.lsgallery_type_personal'),
        );
    }

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
                $sPath = Router::GetPath('gallery') . 'add-images/' . $this->getId();
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

    public function getDefaultCover()
    {
        return Plugin::GetTemplateWebPath('lsgallery') . 'images/default-cover.png';
    }

    public function setId($data)
    {
        $this->_aData['album_id'] = $data;
    }

    public function setUserId($data)
    {
        $this->_aData['album_user_id'] = $data;
    }

    public function setTitle($data)
    {
        $this->_aData['album_title'] = $data;
    }

    public function setDescription($data)
    {
        $this->_aData['album_description'] = $data;
    }

    public function setType($data)
    {
        $this->_aData['album_type'] = $data;
    }

    public function setDateAdd($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H::i:s');
        }
        $this->_aData['album_date_add'] = $data;
    }

    public function setDateEdit($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H::i:s');
        }
        $this->_aData['album_date_edit'] = $data;
    }

    public function setCoverId($data)
    {
        $this->_aData['album_cover_image_id'] = $data;
    }

    public function setImageCount($data)
    {
        $this->_aData['image_count'] = $data;
    }

}