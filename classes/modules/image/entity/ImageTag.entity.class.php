<?php

class PluginLsgallery_ModuleImage_EntityImageTag extends Entity
{

    public function getId()
    {
        return $this->_aData['image_tag_id'];
    }

    public function getImageId()
    {
        return $this->_aData['image_id'];
    }
    
    public function getAlbumId()
    {
        return $this->_aData['album_id'];
    }

    public function getText()
    {
        return $this->_aData['image_tag_text'];
    }

    public function setId($data)
    {
        $this->_aData['image_tag_id'] = $data;
    }

    public function setImageId($data)
    {
        $this->_aData['image_id'] = $data;
    }
    
    public function setAlbumId($data)
    {
        $this->_aData['album_id'] = $data;
    }

    public function setText($data)
    {
        $this->_aData['image_tag_text'] = $data;
    }

}