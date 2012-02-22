<?php

class PluginLsgallery_ModuleUser extends PluginLsgallery_Inherit_ModuleUser
{

    public function GetFriendsByLoginLike($sUserLogin, $iLimit = 10)
    {
        if (!$this->oUserCurrent) {
            return array();
        }

        $data = $this->oMapper->GetFriendsByLoginLike($this->oUserCurrent->getId(), $sUserLogin, $iLimit);
        return $this->GetUsersAdditionalData($data);
    }

}