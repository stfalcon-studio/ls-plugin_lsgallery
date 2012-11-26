<?php

$sDirRoot = dirname(realpath((dirname(__DIR__)) . "/../../"));
set_include_path(get_include_path().PATH_SEPARATOR.$sDirRoot);

require_once($sDirRoot . "/tests/AbstractFixtures.php");


class LsgalleryFixtures extends AbstractFixtures
{
    protected $userId = 1;

    public static function getOrder()
    {
        return 0;
    }

    public function load()
    {
        $this->createAlbum('album opened', 'test album opened description text', 'open');
        $this->createAlbum('album personal', 'test album personal description text', 'personal');   // id 2
        $this->createAlbum('album friend', 'test album friend description text', 'friend');         // id 3

        $this->createImagetoGalery('1', 'test2.jpg');
        $this->createImagetoGalery('1', 'test3.jpg');

        $this->createImagetoGalery('2', 'test4.jpg');
        $this->createImagetoGalery('2', 'test5.jpg');

        $this->createImagetoGalery('3', 'test6.jpg');
        $this->createImagetoGalery('3', 'test8.jpg');
        $this->createImagetoGalery('3', 'test9.jpg');

    }

    private function createImagetoGalery($albumId, $imageName)
    {

        $oUserFirst = $this->getReference('user-first');

        $sDirImg = dirname(realpath((dirname(__DIR__)) . "/../../"));
        $sFileUploadLsgallery = Config::Get('path.uploads.lsgallery_images') . '/';

        $sFile = $sDirImg . "/plugins/lsgallery/tests/fixtures/image/";

        $oImage = Engine::GetEntity('PluginLsgallery_ModuleImage_EntityImage');

        $oImage->setUserId($oUserFirst->getId());
        $oImage->setAlbumId($albumId);

        $sOldFileImg = $sFile . $imageName;
        $sNewFileImg = $sDirImg . $sFileUploadLsgallery . $imageName;

        $saveFilePathName = $sFileUploadLsgallery . $imageName;

        if (copy($sOldFileImg, $sNewFileImg)) {
            $oImage->setFilename($saveFilePathName);
        } else {
            throw new Exception("File Images \" $imageName \" not copy");
        }

        if (!$this->oEngine->PluginLsgallery_Image_AddImage($oImage)) {
            throw new Exception("File \" $imageName \" non saves to base ");
        }
        $this->addReference("image-{$imageName}", $oImage);

        return true;
    }

    /**
     * Create album
     *
     * @param $title string
     * @param $description string
     * @param $type String (personal | open | friend)
     *
     * @return bool Success
     */
    private function createAlbum($title, $description, $type )
    {
        $oUserFirst = $this->getReference('user-first');

        $oAlbum = Engine::GetEntity('PluginLsgallery_Album');
        $oAlbum->setUserId($oUserFirst->getId());
        $oAlbum->setTitle($title);
        $oAlbum->setDescription($description);
        $oAlbum->setType($type);

        if (!$this->oEngine->PluginLsgallery_Album_CreateAlbum($oAlbum)) {
            throw new Exception("Album \" $title \" is not created.");
        }
        $this->addReference("album-{$title}", $oAlbum);

        return true;
    }
}