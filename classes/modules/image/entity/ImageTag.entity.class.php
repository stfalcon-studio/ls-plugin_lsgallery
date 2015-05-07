<?php

/**
 * Class PluginLsgallery_ModuleImage_EntityImageTag
 */
class PluginLsgallery_ModuleImage_EntityImageTag extends Entity
{
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_aData['image_tag_id'];
    }

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
     * Get album id
     *
     * @return int
     */
    public function getAlbumId()
    {
        return $this->_aData['album_id'];
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_aData['image_tag_text'];
    }

    /**
     * Set id
     *
     * @param int $data Image tag id
     */
    public function setId($data)
    {
        $this->_aData['image_tag_id'] = $data;
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
     * Set album id
     *
     * @param int $data Album id
     */
    public function setAlbumId($data)
    {
        $this->_aData['album_id'] = $data;
    }

    /**
     * Set text
     *
     * @param string $data Image tag text
     */
    public function setText($data)
    {
        $this->_aData['image_tag_text'] = $data;
    }
}
