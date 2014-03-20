<?php

class PluginLsgallery_ModuleACL extends PluginCatalog_Inherit_ModuleACL
{

    /**
     * Is allow to create album
     *
     * @param ModuleUser_EntityUser $oUser
     * @return boolean
     */
    public function AllowCreateAlbum($oUser)
    {
        if (!$oUser) {
            return false;
        }

        return true;
    }
    /**
     *
     * Can  create album
     *
     * @param ModuleUser_EntityUser $oUser
     * @return boolean
     */
    public function CanCreateAlbum($oUser)
    {
        if (Config::Get('plugin.lsgallery.aldbum_create_rating') === false) {
            return true;
        }
        if ($oUser->getRating() < Config::Get('plugin.lsgallery.aldbum_create_rating') && !$oUser->isAdministrator()) {
			return false;
		}

        return true;
    }

    /**
     * Is allow to update album
     *
     * @param ModuleUser_EntityUser $oUser
     * @param PluginLsgallery_ModuleAlbum_EntityAlbum $oAlbum
     * @return boolean
     */
    public function AllowUpdateAlbum($oUser, $oAlbum)
    {
        if (!$oUser) {
            return false;
        }


        if ($oUser->isAdministrator()) {
            return true;
        }

        if ($oUser->getId() == $oAlbum->getUserId()) {
            return true;
        }

        return false;
    }

    /**
     * Is allow delete album
     *
     * @param ModuleUser_EntityUser $oUser
     * @param PluginLsgallery_ModuleAlbum_EntityAlbum $oAlbum
     * @return boolean
     */
    public function AllowDeleteAlbum($oUser, $oAlbum)
    {
        if (!$oUser) {
            return false;
        }


        if ($oUser->isAdministrator()) {
            return true;
        }

        if ($oUser->getId() == $oAlbum->getUserId()) {
            return true;
        }

        return false;
    }

    /**
     * Is allow admin album images
     *
     * @param ModuleUser_EntityUser $oUser
     * @param PluginLsgallery_ModuleAlbum_EntityAlbum $oAlbum
     * @return boolean
     */
    public function AllowAdminAlbumImages($oUser, $oAlbum)
    {
        if (!$oUser) {
            return false;
        }


        if ($oUser->isAdministrator()) {
            return true;
        }

        if ($oUser->getId() == $oAlbum->getUserId()) {
            return true;
        }

        if ($oAlbum->getType() == $oAlbum::TYPE_SHARED) {
            return true;
        }

        return false;
    }

    /**
     * Is allow view images from album
     *
     * @param ModuleUser_EntityUser $oUser
     * @param PluginLsgallery_ModuleAlbum_EntityAlbum $oAlbum
     *
     * @return bool
     */
    public function AllowViewAlbumImages($oUser, $oAlbum)
    {
        if ($oAlbum->getType() == PluginLsgallery_ModuleAlbum_EntityAlbum::TYPE_OPEN) {
            return true;
        }

        if ($oAlbum->getType() == PluginLsgallery_ModuleAlbum_EntityAlbum::TYPE_SHARED) {
            return true;
        }

        if (!$oUser) {
            return false;
        }

        if ($oUser->isAdministrator()) {
            return true;
        }

        if ($oUser->getId() == $oAlbum->getUserId()) {
            return true;
        }

        if ($oAlbum->getType() == PluginLsgallery_ModuleAlbum_EntityAlbum::TYPE_PERSONAL) {
            return false;
        }

        if ($oAlbum->getType() == PluginLsgallery_ModuleAlbum_EntityAlbum::TYPE_FRIEND) {
            if ($oFriend = $this->User_GetFriend($oUser->getId(), $oAlbum->getUserId())) {
                if ($oFriend->getFriendStatus() == (ModuleUser::USER_FRIEND_ACCEPT + ModuleUser::USER_FRIEND_ACCEPT)
                        || $oFriend->getFriendStatus() == (ModuleUser::USER_FRIEND_ACCEPT + ModuleUser::USER_FRIEND_OFFER)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Is allow mark user on picture
     *
     * @param ModuleUser_EntityUser $oUserCurrent
     * @param ModuleUser_EntityUser $oUserMarked
     *
     * @return bool
     */
    public function AllowAddUserToImage($oUserCurrent, $oUserMarked)
    {
        if ($oUserCurrent->getId() == $oUserMarked->getId()) {
            return true;
        }
        if ($oFriend = $this->User_GetFriend($oUserCurrent->getId(), $oUserMarked->getUserId())) {
            if ($oFriend->getFriendStatus() == (ModuleUser::USER_FRIEND_ACCEPT + ModuleUser::USER_FRIEND_ACCEPT)
                        || $oFriend->getFriendStatus() == (ModuleUser::USER_FRIEND_ACCEPT + ModuleUser::USER_FRIEND_OFFER)) {
                    return true;
                }
        }
        return false;
    }

    /**
     * Allow update image
     *
     * @param ModuleUser_EntityUser                   $oUser
     * @param PluginLsgallery_ModuleImage_EntityImage $oImage
     *
     * @return bool
     */
    public function AllowUpdateImage($oUser, $oImage)
    {
        if (!$oUser) {
            return false;
        }

        if ($oUser->isAdministrator()) {
            return true;
        }

        if ($oUser->getId() == $oImage->getUserId()) {
            return true;
        }

        if ($oUser->getId() == $oImage->getAlbum()->getUserId()) {
            return true;
        }

        return false;

    }

}