<?php

class PluginLsgallery_ModuleUser_MapperUser extends PluginLsgallery_Inherit_ModuleUser_MapperUser
{

    public function GetFriendsByLoginLike($sUserId, $sUserLogin, $iLimit = 10)
    {
        $sql = "SELECT
					f.*
				FROM
					" . Config::Get('db.table.friend') . " f
                LEFT JOIN
					" . Config::Get('db.table.user') . " as ut ON ut.user_id = f.user_to
                LEFT JOIN
					" . Config::Get('db.table.user') . " as uf ON uf.user_id = f.user_from
				WHERE
					( f.user_from = ?d AND ut.user_login LIKE ? )
					OR
					( f.user_to = ?d AND uf.user_login LIKE ? )
                LIMIT     
                    ?d
				";

        $aRes = array();
        if ($aRows = $this->oDb->select($sql, $sUserId, $sUserLogin . '%', $sUserId,$sUserLogin . '%', $iLimit)) {
            foreach ($aRows as $aRow) {
                if ($aRow['user_to'] == $sUserId) {
                    $aRes[] = $aRow['user_from'];
                } else {
                    $aRes[] = $aRow['user_to'];
                }
            }
        }
        return $aRes;
    }

}
