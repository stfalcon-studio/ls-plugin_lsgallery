<?php

/**
 * @property DbSimple_Generic_Database $oDb
 */
class PluginLsgallery_ModuleImage_MapperImage extends Mapper
{

    /**
     * Add image
     *
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     * @return boolean|int
     */
    public function AddImage($oImage)
    {
        $sql = "INSERT INTO
                    " . Config::Get('db.table.lsgallery.image') . "
                (
                 user_id,
                 album_id,
                 image_filename,
                 image_date_add
                )
                VALUES
                    (?d, ?d, ?, ?)
		";
        if ($iId = $this->oDb->query($sql, $oImage->getUserId(), $oImage->getAlbumId(), $oImage->getFilename(), $oImage->getDateAdd())) {
            return $iId;
        }
        return false;
    }

    /**
     * Update image
     *
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     * @return boolean
     */
    public function UpdateImage($oImage)
    {
        $sql = "UPDATE
                    " . Config::Get('db.table.lsgallery.image') . "
                SET
                    album_id = ?d,
                    image_description = ?,
                    image_tags = ?,
                    image_date_edit = ?,
                    image_count_comment = ?d,
                    image_rating = ?,
                    image_count_vote = ?d,
                    image_count_vote_up= ?d,
				    image_count_vote_down= ?d,
				    image_count_vote_abstain= ?d,
                    image_count_favourite =?d
                WHERE
                    image_id = ?d
                ";
        if ($this->oDb->query($sql, $oImage->getAlbumId(), $oImage->getDescription(), $oImage->getImageTags(), $oImage->getDateEdit(),
                $oImage->getCountComment(), $oImage->getRating(), $oImage->getCountVote(),  $oImage->getCountVoteUp(), $oImage->getCountVoteDown(),
                $oImage->getCountVoteAbstain(), $oImage->getCountFavourite(), $oImage->getId())) {
            return true;
        }
        return false;
    }

    /**
     * Add image tag
     *
     * @param PluginLsgallery_ModuleImage_EntityImageTag $oImageTag
     * @return boolean
     */
    public function AddImageTag($oImageTag)
    {
        $sql = "INSERT INTO
                    " . Config::Get('db.table.lsgallery.image_tag') . "
                (
                image_id,
                album_id,
                image_tag_text
                )
                VALUES
                    (?d, ?d, ?)
                ";
        if ($iId = $this->oDb->query($sql, $oImageTag->getImageId(), $oImageTag->getAlbumId(), $oImageTag->getText())) {
            return $iId;
        }
        return false;
    }

    /**
     * Delete tags by image id
     * @param int $sImageId
     * @return boolean
     */
    public function DeleteImageTagsByImageId($sImageId)
    {
        $sql = "DELETE FROM
                    " . Config::Get('db.table.lsgallery.image_tag') . "
                WHERE
                    image_id = ?d
                ";
        if ($this->oDb->query($sql, $sImageId)) {
            return true;
        }
        return false;
    }

    /**
     * Get image tags by text like with limit
     *
     * @param string $sTag
     * @param int $iLimit
     * @return \PluginLsgallery_ModuleImage_EntityImageTag
     */
    public function GetImageTagsByLike($sTag, $iLimit)
    {
        $sTag = mb_strtolower($sTag, "UTF-8");
        $sql = "SELECT
                    *
                FROM
                    " . Config::Get('db.table.lsgallery.image_tag') . "
                WHERE
                    image_tag_text LIKE ?
                GROUP BY
                    image_tag_text
                LIMIT 0, ?d
				";
        $aReturn = array();
        if ($aRows = $this->oDb->select($sql, $sTag . '%', $iLimit)) {
            foreach ($aRows as $aRow) {
                $aReturn[] = new PluginLsgallery_ModuleImage_EntityImageTag($aRow);
            }
        }
        return $aReturn;
    }

    /**
     * Delete
     *
     * @param int $iImageId
     * @return boolean|int
     */
    public function DeleteImage($iImageId)
    {
        $sql = "DELETE FROM
                    " . Config::Get('db.table.lsgallery.image') . "
                WHERE
                    image_id = ?d
                ";
        return $this->oDb->query($sql, $iImageId);
    }

    /**
     * Get images by array id
     * @param array $aArrayId
     * @return \PluginLsgallery_ModuleImage_EntityImage
     */
    public function GetImagesByArrayId($aArrayId)
    {
        if (!is_array($aArrayId) or count($aArrayId) == 0) {
            return array();
        }

        $sql = "SELECT
                        *
                FROM
                        " . Config::Get('db.table.lsgallery.image') . "
                WHERE
                        image_id IN(?a)
                ORDER BY
                        FIELD(image_id,?a)";

        $aImages = array();
        if ($aRows = $this->oDb->select($sql, $aArrayId, $aArrayId)) {
            foreach ($aRows as $aRow) {
                $aImages[] = new PluginLsgallery_ModuleImage_EntityImage($aRow);
            }
        }
        return $aImages;
    }

    /**
     * Get images id by filter with paging
     *
     * @param array $aFilter
     * @param int $iCount
     * @param int $iCurrPage
     * @param int $iPerPage
     * @return array
     */
    public function GetImages($aFilter, &$iCount, $iCurrPage, $iPerPage)
    {
        $sWhere = $this->buildFilter($aFilter);

        if (!isset($aFilter['order'])) {
            $aFilter['order'] = 'i.image_date_add desc';
        }
        if (!is_array($aFilter['order'])) {
            $aFilter['order'] = array($aFilter['order']);
        }

        $sql = "SELECT
                    i.image_id
                FROM
                    " . Config::Get('db.table.lsgallery.image') . " as i
                LEFT JOIN
                    " . Config::Get('db.table.lsgallery.album') . " as a ON a.album_id = i.album_id
                WHERE
                    1=1
                    " . $sWhere . "
                GROUP BY
                    i.image_id
                ORDER BY " .
                implode(', ', $aFilter['order']) . "
                LIMIT
                    ?d, ?d";
        $aImages = array();
        if ($aRows = $this->oDb->selectPage($iCount, $sql, ($iCurrPage - 1) * $iPerPage, $iPerPage)) {
            foreach ($aRows as $aRow) {
                $aImages[] = $aRow['image_id'];
            }
        }
        return $aImages;
    }

    /**
     * Get count images
     * @param array $aFilter
     * @return int
     */
    public function GetCountImages($aFilter)
    {
        $sWhere = $this->buildFilter($aFilter);
        $sql = "SELECT
                    count(i.image_id) as count
                FROM
                    " . Config::Get('db.table.lsgallery.image') . " as i
                LEFT JOIN
                    " . Config::Get('db.table.lsgallery.album') . " as a ON a.album_id = i.album_id
                WHERE
                    1=1
                " . $sWhere;
        if ($aRow = $this->oDb->selectRow($sql)) {
            return $aRow['count'];
        }
        return false;
    }

    /**
     * Get all images by filter
     *
     * @param array $aFilter
     * @return array
     */
    public function GetAllImages($aFilter)
    {
        $sWhere = $this->buildFilter($aFilter);

        if (!isset($aFilter['order'])) {
            $aFilter['order'] = 'i.image_id desc';
        }
        if (!is_array($aFilter['order'])) {
            $aFilter['order'] = array($aFilter['order']);
        }

        $sql = "SELECT
                    i.image_id
                FROM
                    " . Config::Get('db.table.lsgallery.image') . " as i
                LEFT JOIN
                    " . Config::Get('db.table.lsgallery.album') . " as a ON a.album_id = i.album_id
                WHERE
                    1=1
                " . $sWhere . "
                GROUP BY
                    i.image_id
                ORDER BY
                    " . implode(', ', $aFilter['order']) . " ";
        $aImages = array();
        if ($aRows = $this->oDb->select($sql)) {
            foreach ($aRows as $aRow) {
                $aImages[] = $aRow['image_id'];
            }
        }

        return $aImages;
    }

    /**
     * Build filter for sql query
     *
     * @param array $aFilter
     * @return string
     */
    protected function buildFilter($aFilter)
    {
        $sWhere = '';

        if (isset($aFilter['image_rating']) and is_array($aFilter['image_rating'])) {

            if ($aFilter['image_rating']['type'] == 'top') {
                $sWhere.=" AND ( i.image_rating >= " . (float) $aFilter['image_rating']['value'] . " ) ";
            } else {
                $sWhere.=" AND ( i.image_rating < " . (float) $aFilter['image_rating']['value'] . "  ) ";
            }
        }


        if (isset($aFilter['image_new'])) {
            $sWhere.=" AND i.image_date_add >=  '" . $aFilter['image_new'] . "'";
        }

        if (isset($aFilter['user_id'])) {
            $sWhere .= is_array($aFilter['user_id']) ? " AND i.user_id IN(" . implode(', ', $aFilter['user_id']) . ")" : " AND i.user_id =  " . (int) $aFilter['user_id'];
        }

        if (isset($aFilter['album_id'])) {
            if (!is_array($aFilter['album_id'])) {
                $aFilter['album_id'] = array($aFilter['album_id']);
            }
            $sWhere.=" AND i.album_id IN ('" . join("','", $aFilter['album_id']) . "')";
        }

        if (isset($aFilter['album_type']) and is_array($aFilter['album_type'])) {
            $aAlbumTypes = array();
            foreach ($aFilter['album_type'] as $sType => $aAlbumId) {
                /**
                 * Позиция вида 'type'=>array('id1', 'id2')
                 */
                if (!is_array($aAlbumId) && is_string($sType)) {
                    $aAlbumId = array($aAlbumId);
                }
                /**
                 * Позиция вида 'type'
                 */
                if (is_string($aAlbumId) && is_int($sType)) {
                    $sType = $aAlbumId;
                    $aAlbumId = array();
                }

                $aAlbumTypes[] = (count($aAlbumId) == 0) ? "(a.album_type='" . $sType . "')" : "(a.album_type='" . $sType . "' AND a.album_user_id IN ('" . join("','", $aAlbumId) . "'))";
            }
            $sWhere.=" AND (" . join(" OR ", (array) $aAlbumTypes) . ")";
        }
        return $sWhere;
    }

    /**
     * Update image read
     *
     * @param PluginLsgallery_ModuleImage_EntityImageRead $oImageRead
     * @return boolean
     */
    public function UpdateImageRead($oImageRead)
    {
        $sql = "UPDATE
                    " . Config::Get('db.table.lsgallery.image_read') . "
                SET
                    comment_count_last = ? ,
                    comment_id_last = ? ,
                    date_read = ?
                WHERE
                    image_id = ?
                    AND
                    user_id = ?
                ";
        return $this->oDb->query($sql, $oImageRead->getCommentCountLast(), $oImageRead->getCommentIdLast(), $oImageRead->getDateRead(), $oImageRead->getImageId(), $oImageRead->getUserId());
    }

    /**
     * Add image read
     *
     * @param PluginLsgallery_ModuleImage_EntityImageRead $oImageRead
     * @return boolean
     */
    public function AddImageRead($oImageRead)
    {
        $sql = "INSERT INTO
                    " . Config::Get('db.table.lsgallery.image_read') . "
                SET
                    comment_count_last = ? ,
                    comment_id_last = ? ,
                    date_read = ? ,
                    image_id = ? ,
                    user_id = ?
                ";
        return $this->oDb->query($sql, $oImageRead->getCommentCountLast(), $oImageRead->getCommentIdLast(), $oImageRead->getDateRead(), $oImageRead->getImageId(), $oImageRead->getUserId());
    }

    /**
     * Delete image read by array image id
     * @param array $aImageId
     * @return boolean
     */
    public function DeleteImageReadByArrayId($aImageId)
    {
        $sql = "DELETE FROM
                    " . Config::Get('db.table.lsgallery.image_read') . "
                WHERE
                    image_id IN(?a)
                ";
        if ($this->oDb->query($sql, $aImageId)) {
            return true;
        }
        return false;
    }

    /**
     * Get images read by array image and user
     *
     * @param array $aArrayId
     * @param string $sUserId
     * @return \PluginLsgallery_ModuleImage_EntityImageRead
     */
    public function GetImagesReadByArray($aArrayId, $sUserId)
    {
        if (!is_array($aArrayId) or count($aArrayId) == 0) {
            return array();
        }

        $sql = "SELECT
                        ir.*
                FROM
                        " . Config::Get('db.table.lsgallery.image_read') . " as ir
                WHERE
                    ir.image_id IN(?a)
                AND
                    ir.user_id = ?d
                ";
        $aReads = array();
        if ($aRows = $this->oDb->select($sql, $aArrayId, $sUserId)) {
            foreach ($aRows as $aRow) {
                $aReads[] = new PluginLsgallery_ModuleImage_EntityImageRead($aRow);
            }
        }
        return $aReads;
    }

    /**
     * Increase image count comment
     *
     * @param string $sImageId
     * @return boolean
     */
    public function IncreaseImageCountComment($sImageId)
    {
        $sql = "UPDATE
                    " . Config::Get('db.table.lsgallery.image') . "
                SET
                    image_count_comment = image_count_comment + 1
                WHERE
                    image_id = ?d
		";
        if ($this->oDb->query($sql, $sImageId)) {
            return true;
        }
        return false;
    }

    /**
     * Get random images id
     *
     * @param int $limit
     * @return array
     */
    public function GetRandomImages($limit)
    {
        $sql = "SELECT
                    i.image_id
                FROM
                    " . Config::Get('db.table.lsgallery.image') . " as i
                LEFT JOIN
                    " . Config::Get('db.table.lsgallery.album') . " as a ON a.album_id = i.album_id
                WHERE
                    a.album_type = 'open'
				ORDER BY
                    RAND()
                LIMIT ?d
                ";
        $aImages = array();
        if ($aRows = $this->oDb->select($sql, $limit)) {
            foreach ($aRows as $aRow) {
                $aImages[] = $aRow['image_id'];
            }
        }

        return $aImages;
    }

    /**
     * Get tags of open image
     *
     * @param int $iLimit
     * @return \PluginLsgallery_ModuleImage_EntityImageTag
     */
    public function GetOpenImageTags($iLimit)
    {
        $sql = "
                SELECT
                    it.image_tag_text,
                    count(it.image_tag_text)	as count
                FROM
                    " . Config::Get('db.table.lsgallery.image_tag') . "  as it
                LEFT JOIN
                    " . Config::Get('db.table.lsgallery.album') . " as a ON a.album_id = it.album_id
                WHERE
                    a.album_type  = 'open'
                GROUP BY
                    it.image_tag_text
                ORDER BY
                    count desc
                LIMIT 0, ?d
                ";
        $aReturn = array();
        $aReturnSort = array();
        if ($aRows = $this->oDb->select($sql, $iLimit)) {
            foreach ($aRows as $aRow) {
                $aReturn[mb_strtolower($aRow['image_tag_text'], 'UTF-8')] = $aRow;
            }
            ksort($aReturn);
            foreach ($aReturn as $aRow) {
                $aReturnSort[] = new PluginLsgallery_ModuleImage_EntityImageTag($aRow);
            }
        }
        return $aReturnSort;
    }

    /**
     * Get images by tag
     *
     * @param string $sTag
     * @param int $iCount
     * @param int $iCurrPage
     * @param int $iPerPage
     * @return array
     */
    public function GetImagesByTag($sTag, &$iCount, $iCurrPage, $iPerPage)
    {
        $sql = "SELECT
                    image_id
                FROM
                    " . Config::Get('db.table.lsgallery.image_tag') . "  as it
                LEFT JOIN
                    " . Config::Get('db.table.lsgallery.album') . " as a ON a.album_id = it.album_id
                WHERE
                    a.album_type  = 'open'
				 AND
                    image_tag_text = ?
                ORDER BY image_id DESC
                LIMIT ?d, ?d ";

        $aImages = array();
        if ($aRows = $this->oDb->selectPage($iCount, $sql, $sTag, ($iCurrPage - 1) * $iPerPage, $iPerPage)) {
            foreach ($aRows as $aRow) {
                $aImages[] = $aRow['image_id'];
            }
        }
        return $aImages;
    }

    /**
     * Get favourite open images by user
     *
     * @param string $sUserId
     * @param int $iCount
     * @param int $iCurrPage
     * @param int $iPerPage
     * @return array
     */
    public function GetFavouriteOpenImagesByUserId($sUserId, &$iCount, $iCurrPage, $iPerPage)
    {
        $sql = "
                SELECT
                    f.target_id
                FROM
                    " . Config::Get('db.table.favourite') . " AS f
                LEFT JOIN
                        " . Config::Get('db.table.lsgallery.image') . " as i ON i.image_id = f.target_id
                LEFT JOIN
                        " . Config::Get('db.table.lsgallery.album') . " as a ON a.album_id = i.album_id
                WHERE
                                f.user_id = ?d
                        AND
                                f.target_publish = 1
                        AND
                                f.target_type = 'image'
                        AND
                                a.album_type = 'open'
                GROUP BY target_id
                ORDER BY target_id DESC
                LIMIT ?d, ?d ";

        $aFavourites = array();
        if ($aRows = $this->oDb->selectPage(
                $iCount, $sql, $sUserId, ($iCurrPage - 1) * $iPerPage, $iPerPage
        )) {
            foreach ($aRows as $aFavourite) {
                $aFavourites[] = $aFavourite['target_id'];
            }
        }
        return $aFavourites;
    }

    /**
     * Get count open fav images by user
     *
     * @param type $sUserId
     * @return int
     */
    public function GetCountFavouriteOpenImagesByUserId($sUserId)
    {
        $sql = "
                SELECT
                    count(f.target_id) as count
                FROM
                    " . Config::Get('db.table.favourite') . " AS f
                LEFT JOIN
                        " . Config::Get('db.table.lsgallery.image') . " as i ON i.image_id = f.target_id
                LEFT JOIN
                        " . Config::Get('db.table.lsgallery.album') . " as a ON a.album_id = i.album_id
                WHERE
                        f.user_id = ?d
                AND
                        f.target_publish = 1
                AND
                        f.target_type = 'image'
                AND
                        a.album_type = 'open'
                ";

        if ($aRow = $this->oDb->selectRow($sql, $sUserId)) {
            return $aRow['count'];
        }
        return null;
    }

    /**
     * Add image user
     *
     * @param PluginLsgallery_ModuleImage_EntityImageUser $oImageUser
     * @return boolean|int
     */
    public function AddImageUser($oImageUser)
    {
        $sql = "INSERT INTO
                    " . Config::Get('db.table.lsgallery.image_user') . "
                (
                 image_id,
                 user_id,
                 target_user_id,
                 lasso_x,
                 lasso_y,
                 lasso_w,
                 lasso_h,
                 status
                )
                VALUES
                    (?d, ?d, ?d, ?d, ?d, ?d, ?d, ?)
		";
        return $this->oDb->query($sql, $oImageUser->getImageId(), $oImageUser->getUserId(), $oImageUser->getTargetUserId(), $oImageUser->getLassoX(), $oImageUser->getLassoY(), $oImageUser->getLassoW(), $oImageUser->getLassoH(), $oImageUser->getStatus());
    }

    /**
     * Get image user
     *
     * @param string $sUserId
     * @param string $sImageId
     * @return PluginLsgallery_ModuleImage_EntityImageUser
     */
    public function GetImageUser($sUserId, $sImageId)
    {
        $sql = "
                SELECT
                    *
                FROM
                        " . Config::Get('db.table.lsgallery.image_user') . "
                WHERE
                    target_user_id = ?d
                AND
                    image_id = ?d
            ";

        if ($aRow = $this->oDb->selectRow($sql, $sUserId, $sImageId)) {
            return new PluginLsgallery_ModuleImage_EntityImageUser($aRow);
        }
        return null;
    }

    /**
     * Get image users
     *
     * @param string $sImageId
     * @return \PluginLsgallery_ModuleImage_EntityImageUser
     */
    public function GetImageUsersByImageId($sImageId)
    {
        $sql = "
                SELECT
                    *
                FROM
                    " . Config::Get('db.table.lsgallery.image_user') . "
                WHERE
                    image_id = ?d
            ";

        $aResult = array();
        if ($aRows = $this->oDb->select($sql, $sImageId)) {
            foreach ($aRows as $aRow) {
                $aResult[] = new PluginLsgallery_ModuleImage_EntityImageUser($aRow);
            }
        }
        return $aResult;
    }

    /**
     * Change image user status
     *
     * @param PluginLsgallery_ModuleImage_EntityImageUser $oImageUser
     * @return boolean
     */
    public function ChangeStatusImageUser($oImageUser)
    {
        $sql = "UPDATE
                    " . Config::Get('db.table.lsgallery.image_user') . "
                SET
                    status = ?
                WHERE
                    image_id = ?d
                AND
                    target_user_id = ?d
		";
        if ($this->oDb->query($sql, $oImageUser->getStatus(), $oImageUser->getImageId(), $oImageUser->getTargetUserId())) {
            return true;
        }
        return false;
    }

    /**
     * Delete image user status
     *
     * @param PluginLsgallery_ModuleImage_EntityImageUser $oImageUser
     * @return boolean
     */
    public function DeleteImageUser($oImageUser)
    {
        $sql = "DELETE FROM
                    " . Config::Get('db.table.lsgallery.image_user') . "
                WHERE
                    image_id = ?d
                AND
                    target_user_id = ?d
		";
        if ($this->oDb->query($sql, $oImageUser->getImageId(), $oImageUser->getTargetUserId())) {
            return true;
        }
        return false;
    }

    public function GetImagesByUserMarked($sUserId, &$iCount, $iCurrPage, $iPerPage)
    {
        $sql = "
                SELECT
                    image_id
                FROM
                    " . Config::Get('db.table.lsgallery.image_user') . "
                WHERE
                        target_user_id = ?d
                AND
                        status = 'confirmed'
                LIMIT ?d, ?d ";

        $aImages = array();
        if ($aRows = $this->oDb->selectPage(
                $iCount, $sql, $sUserId, ($iCurrPage - 1) * $iPerPage, $iPerPage
        )) {
            foreach ($aRows as $aRow) {
                $aImages[] = $aRow['image_id'];
            }
        }
        return $aImages;
    }

    /**
     * Get prev image id
     *
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     * @return int|null
     */
    public function GetPrevImageId($oImage)
    {
        $sql = "
                SELECT
                    image_id
                FROM
                    " . Config::Get('db.table.lsgallery.image') . "
                WHERE
                        image_id < ?d
                AND
                        album_id = ?d
                ORDER BY
                    image_id DESC
            ";

        if ($aRow = $this->oDb->selectRow($sql, $oImage->getId(), $oImage->getAlbumId())) {
            return $aRow['image_id'];
        }
        return null;
    }

    /**
     * Get next image id
     *
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     * @return int|null
     */
    public function GetNextImageId($oImage)
    {
        $sql = "
                SELECT
                    image_id
                FROM
                    " . Config::Get('db.table.lsgallery.image') . "
                WHERE
                    image_id > ?d
                AND
                    album_id = ?d
                ORDER BY
                    image_id ASC
                ";

        if ($aRow = $this->oDb->selectRow($sql, $oImage->getId(), $oImage->getAlbumId())) {
            return $aRow['image_id'];
        }
        return null;
    }

}