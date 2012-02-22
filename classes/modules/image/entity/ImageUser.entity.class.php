<?php

class PluginLsgallery_ModuleImage_EntityImageUser extends Entity
{

    const STATUS_NEW = 'new';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_DECLINED = 'declined';

    public function getImageId()
    {
        return $this->_aData['image_id'];
    }

    public function getUserId()
    {
        return $this->_aData['user_id'];
    }

    public function getTargetUserId()
    {
        return $this->_aData['target_user_id'];
    }

    public function getLassoX()
    {
        return $this->_aData['lasso_x'];
    }

    public function getLassoY()
    {
        return $this->_aData['lasso_y'];
    }

    public function getLassoW()
    {
        return $this->_aData['lasso_w'];
    }

    public function getLassoH()
    {
        return $this->_aData['lasso_h'];
    }

    public function getStatus()
    {
        return $this->_aData['status'];
    }
    
    public function getTargetUser()
    {
        if (!isset($this->_aData['target_user'])) {
            $this->_aData['target_user'] = $this->User_GetUserById($this->getTargetUserId());
        }
        
        return $this->_aData['target_user'];
    }
    public function setImageId($data)
    {
        $this->_aData['image_id'] = $data;
    }

    public function setUserId($data)
    {
        $this->_aData['user_id'] = $data;
    }

    public function setTargertUserId($data)
    {
        $this->_aData['target_user_id'] = $data;
    }

    public function setLassoX($data)
    {
        $this->_aData['lasso_x'] = $data;
    }

    public function setLassoY($data)
    {
        $this->_aData['lasso_y'] = $data;
    }

    public function setLassoW($data)
    {
        $this->_aData['lasso_w'] = $data;
    }

    public function setLassoH($data)
    {
        $this->_aData['lasso_h'] = $data;
    }

    public function setStatus($data)
    {
        $this->_aData['status'] = $data;
    }

}