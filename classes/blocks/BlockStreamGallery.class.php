<?php

class PluginLsgallery_BlockStreamGallery extends Block
{

    public function Exec()
    {
        $aResult = $this->PluginLsgallery_Image_GetImagesNew(1, Config::Get('plugin.lsgallery.image_row'));
        if ($aResult['count']) {
            $aImages = $aResult['collection'];
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign('aImages', $aImages);
            $oViewer->Assign('sType', 'new');
            $sTextResult = $oViewer->Fetch(Plugin::GetTemplatePath('lsgallery') . "block.stream_photo.tpl");
            $this->Viewer_Assign('sStreamImages', $sTextResult);
        }
    }

}