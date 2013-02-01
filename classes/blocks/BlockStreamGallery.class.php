<?php

class PluginLsgallery_BlockStreamGallery extends Block
{

    public function Exec()
    {
        $aResult = $this->PluginLsgallery_Image_GetImagesBest(1, Config::Get('plugin.lsgallery.image_row'));
        if ($aResult['count']) {
            $aImages = $aResult['collection'];
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign('aImages', $aImages);
            $oViewer->Assign('sType', 'best');
            $oViewer->Assign('oUserCurrent', $this->oUserCurrent);
            $sTextResult = $oViewer->Fetch(Plugin::GetTemplatePath('lsgallery') . "block.stream_photo.tpl");
            $this->Viewer_Assign('sBestImages', $sTextResult);
        }

//        $aResult = $this->PluginLsgallery_Image_GetImagesNew(1, Config::Get('plugin.lsgallery.image_row'));
//        if ($aResult['count']) {
//            $aImages = $aResult['collection'];
//            $oViewer = $this->Viewer_GetLocalViewer();
//            $oViewer->Assign('aImages', $aImages);
//            $oViewer->Assign('sType', 'new');
//            $oViewer->Assign('oUserCurrent', $this->User_GetUserCurrent());
//            $sTextResult = $oViewer->Fetch(Plugin::GetTemplatePath('lsgallery') . "block.stream_photo.tpl");
//            $this->Viewer_Assign('sStreamImages', $sTextResult);
//        }
    }

}