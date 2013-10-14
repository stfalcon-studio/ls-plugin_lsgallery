<?php

/**
 * @property DbSimple_Generic_Database $oDb
 */
class PluginLsgallery_ModuleAlbum_MapperAlbum extends Mapper
{

    /**
     * Create album
     *
     * @param PluginLsgallery_ModuleAlbum_EntityAlbum $oAlbum
     * @return boolean|PluginLsgallery_ModuleAlbum_EntityAlbum
     */
    public function CreateAlbum($oAlbum)
    {
        $sql = "INSERT INTO
                    " . Config::Get('db.table.lsgallery.album') . "
                (
                 album_user_id,
                 album_title,
                 album_description,
                 album_type,
                 album_date_add
                )
                VALUES
                    (?d, ?, ?, ?, ?)";
        if ($iId = $this->oDb->query($sql, $oAlbum->getUserId(), $oAlbum->getTitle(), $oAlbum->getDescription(), $oAlbum->getType(), $oAlbum->getDateAdd())) {
            return $iId;
        }
        return false;
    }

    /**
     * Update album
     *
     * @param PluginLsgallery_ModuleAlbum_EntityAlbum $oAlbum
     * @return boolean
     */
    public function UpdateAlbum($oAlbum)
    {
        $sql = "UPDATE
                    " . Config::Get('db.table.lsgallery.album') . "
                SET
                    album_title = ?,
                    album_description = ?,
                    album_type = ?,
                    album_date_edit = ?,
                    album_cover_image_id= ?d,
                    image_count = ?d
                WHERE
                    album_id = ?d
                ";
        if ($this->oDb->query($sql, $oAlbum->getTitle(), $oAlbum->getDescription(), $oAlbum->getType(), $oAlbum->getDateEdit(), $oAlbum->getCoverId(), $oAlbum->getImageCount(), $oAlbum->getId())) {
            return true;
        }
        return false;
    }

    /**
     * Delete album by id
     *
     * @param int $iAlbumId
     * @return boolean
     */
    public function DeleteAlbum($iAlbumId)
    {
        $sql = "DELETE FROM
                    " . Config::Get('db.table.lsgallery.album') . "
                WHERE
                    album_id = ?d
            ";
        if ($this->oDb->query($sql, $iAlbumId)) {
            return true;
        }
        return false;
    }

    /**
     * Get albums by array id
     * @param array $aArrayId
     * @return \PluginLsgallery_ModuleAlbum_EntityAlbum
     */
    public function GetAlbumsByArrayId($aArrayId)
    {
        if (!is_array($aArrayId) or count($aArrayId) == 0) {
            return array();
        }

        $sql = "SELECT
                        *
                FROM
                        " . Config::Get('db.table.lsgallery.album') . "
                WHERE
                        album_id IN(?a)
                ORDER BY
                        FIELD(album_id,?a)";

        $aAlbums = array();
        if ($aRows = $this->oDb->select($sql, $aArrayId, $aArrayId)) {
            foreach ($aRows as $aRow) {
                $aAlbums[] = Engine::GetEntity('PluginLsgallery_ModuleAlbum_EntityAlbum',$aRow);
            }
        }
        return $aAlbums;
    }

    /**
     * Get albums by filter paging
     *
     * @param array $aFilter
     * @param int $iCount
     * @param int $iCurrPage
     * @param int $iPerPage
     * @return array
     */
    public function GetAlbums($aFilter, &$iCount, $iCurrPage, $iPerPage)
    {
        $sWhere = $this->buildFilter($aFilter);

        if (!isset($aFilter['order'])) {
            $aFilter['order'] = 'a.album_date_add desc';
        }
        if (!is_array($aFilter['order'])) {
            $aFilter['order'] = array($aFilter['order']);
        }

        $sql = "SELECT
                    a.album_id
                FROM
                    " . Config::Get('db.table.lsgallery.album') . " as a
                WHERE
                    1=1
                    " . $sWhere . "
                GROUP BY
                    a.album_id
                ORDER BY " .
                implode(', ', $aFilter['order']) . "
                LIMIT
                    ?d, ?d";
        $aAlbums = array();
        if ($aRows = $this->oDb->selectPage($iCount, $sql, ($iCurrPage - 1) * $iPerPage, $iPerPage)) {
            foreach ($aRows as $aRow) {
                $aAlbums[] = $aRow['album_id'];
            }
        }
        return $aAlbums;
    }

    /**
     * get count albums by filter
     *
     * @param array $aFilter
     * @return int
     */
    public function GetCountAlbums($aFilter)
    {
        $sWhere = $this->buildFilter($aFilter);
        $sql = "SELECT
                    count(a.album_id) as count
                FROM
                    " . Config::Get('db.table.lsgallery.album') . " as a
                WHERE
                        1=1
                " . $sWhere;
        if ($aRow = $this->oDb->selectRow($sql)) {
            return $aRow['count'];
        }
        return false;
    }

    /**
     * Get albums by filter
     *
     * @param array $aFilter
     * @return array
     */
    public function GetAllAlbums($aFilter)
    {
        $sWhere = $this->buildFilter($aFilter);

        if (!isset($aFilter['order'])) {
            $aFilter['order'] = 'a.album_id desc';
        }
        if (!is_array($aFilter['order'])) {
            $aFilter['order'] = array($aFilter['order']);
        }

        $sql = "SELECT
                    a.album_id
                FROM
                    " . Config::Get('db.table.lsgallery.album') . " as a
                WHERE
                    1=1
                " . $sWhere . "
                GROUP BY
                    a.album_id
                ORDER BY
                    " . implode(', ', $aFilter['order']) . " ";
        $aAlbums = array();
        if ($aRows = $this->oDb->select($sql)) {
            foreach ($aRows as $aRow) {
                $aAlbums[] = $aRow['album_id'];
            }
        }
        return $aAlbums;
    }

    /**
     * Get close albums id
     *
     * @return array
     */
    public function GetCloseAlbums()
    {
        $sql = "SELECT
                    a.album_id
                FROM
                    " . Config::Get('db.table.lsgallery.album') . " as a
                WHERE
                    a.album_type <> 'open'
                GROUP BY
                    a.album_id
                ";
        $aAlbums = array();
        if ($aRows = $this->oDb->select($sql)) {
            foreach ($aRows as $aRow) {
                $aAlbums[] = $aRow['album_id'];
            }
        }
        return $aAlbums;
    }

    /**
     * Build sql filter
     *
     * @param array $aFilter
     * @return string
     */
    protected function buildFilter($aFilter)
    {
        $sWhere = '';

        if (isset($aFilter['album_new'])) {
            $sWhere.=" AND a.album_date_add >=  '" . $aFilter['album_new'] . "'";
        }

        if (isset($aFilter['not_empty'])) {
            $sWhere.=" AND a.image_count > 0 ";
        }

        if (isset($aFilter['user_id'])) {
            $sWhere .= is_array($aFilter['user_id']) ? " AND a.album_user_id IN(" . implode(', ', $aFilter['user_id']) . ")" : " AND a.album_user_id =  " . (int) $aFilter['user_id'];
        }

        if (isset($aFilter['album_type']) and is_array($aFilter['album_type'])) {
            $aAlbumTypes = array();
            foreach ($aFilter['album_type'] as $sType => $aAlbumId) {
                if ($sType == 'open') {
                    $aAlbumTypes[] = "a.album_type = 'open'";
                } else if ($sType == 'friend' && is_array($aAlbumId)) {
                    $aAlbumTypes[] = "(a.album_type = 'friend'  AND a.album_user_id IN (" . implode(', ', $aAlbumId) . ")) ";
                } else if ($sType == 'personal') {
                    $aAlbumTypes[] = "a.album_type = 'personal'";
                }
            }
            $sWhere.=" AND (" . join(" OR ", (array) $aAlbumTypes) . ")";
        }

        if(isset($aFilter['title'])){
            $sWhere .= " AND album_title = '".$this->oDb->escape($aFilter['title'])."'";
        }

        return $sWhere;
    }

}