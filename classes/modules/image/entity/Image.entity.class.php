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

    public function getNextImageId()
    {
        return $this->_aData['next_image_id'];
    }

    public function getPrevImageId()
    {
        return $this->_aData['prev_image_id'];
    }

    public function getCountComment()
    {
        return $this->_aData['image_count_comment'];
    }

    public function getRating()
    {
        return number_format(round($this->_aData['image_rating'],2), 0, '.', '');
    }

    public function getCountVote()
    {
        return $this->_aData['image_count_vote'];
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
                return $aPathInfo['dirname'] . '/' . $aPathInfo['filename'] . '_' . $sWidth . '.' . $aPathInfo['extension'];
            } else {
                return $this->getFilename();
            }
        } else {
            return null;
        }
    }

    public function getNextImage()
    {
        if (!isset($this->_aData['next_image'])) {
            if (is_null($this->getNextImageId())) {
                $this->_aData['next_image'] = null;
            } else {
                $this->_aData['next_image'] = $this->PluginLsgallery_Image_GetImageById($this->getNextImageId());
            }
        }

        return $this->_aData['next_image'];
    }

    public function getPrevImage()
    {
        if (!isset($this->_aData['prev_image'])) {
            if (is_null($this->getPrevImageId())) {
                $this->_aData['prev_image'] = null;
            } else {
                $this->_aData['prev_image'] = $this->PluginLsgallery_Image_GetImageById($this->getPrevImageId());
            }
        }

        return $this->_aData['prev_image'];
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

    public function setNextImageId($data)
    {
        $this->_aData['next_image_id'] = $data;
    }

    public function setPrevImageId($data)
    {
        $this->_aData['prev_image_id'] = $data;
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

}