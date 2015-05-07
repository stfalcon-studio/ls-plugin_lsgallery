<?php

/**
 * Class PluginLsgallery_ModuleImage_EntityImageRead
 */
class PluginLsgallery_ModuleImage_EntityImageRead extends Entity
{
    /**
     * Get image id
     *
     * @return int
     */
    public function getImageId()
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
     * Get date read
     *
     * @return datetime
     */
    public function getDateRead()
    {
        return $this->_aData['date_read'];
    }

    /**
     * Get comment count last
     *
     * @return int
     */
    public function getCommentCountLast()
    {
        return $this->_aData['comment_count_last'];
    }

    /**
     * Get comment id last
     *
     * @return int
     */
    public function getCommentIdLast()
    {
        return $this->_aData['comment_id_last'];
    }

    /**
     * Set image id
     *
     * @param int $data Image id
     */
    public function setImageId($data)
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
     * Set date read
     *
     * @param datetime $data Date read
     */
    public function setDateRead($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H::i:s');
        }
        $this->_aData['date_read'] = $data;
    }

    /**
     * Set comment count last
     *
     * @param int $data Comment count last
     */
    public function setCommentCountLast($data)
    {
        $this->_aData['comment_count_last'] = $data;
    }

    /**
     * Set comment id last
     *
     * @param int $data Comment id last
     */
    public function setCommentIdLast($data)
    {
        $this->_aData['comment_id_last'] = $data;
    }
}
