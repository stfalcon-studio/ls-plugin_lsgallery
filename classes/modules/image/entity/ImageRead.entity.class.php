<?php

class PluginLsgallery_ModuleImage_EntityImageRead extends Entity
{

    public function getImageId()
    {
        return $this->_aData['image_id'];
    }

    public function getUserId()
    {
        return $this->_aData['user_id'];
    }

    public function getDateRead()
    {
        return $this->_aData['date_read'];
    }

    public function getCommentCountLast()
    {
        return $this->_aData['comment_count_last'];
    }

    public function getCommentIdLast()
    {
        return $this->_aData['comment_id_last'];
    }

    public function setImageId($data)
    {
        $this->_aData['image_id'] = $data;
    }

    public function setUserId($data)
    {
        $this->_aData['user_id'] = $data;
    }

    public function setDateRead($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H::i:s');
        }
        $this->_aData['date_read'] = $data;
    }

    public function setCommentCountLast($data)
    {
        $this->_aData['comment_count_last'] = $data;
    }

    public function setCommentIdLast($data)
    {
        $this->_aData['comment_id_last'] = $data;
    }

}