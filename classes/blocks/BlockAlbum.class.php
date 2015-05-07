<?php

/**
 * Class PluginLsgallery_BlockAlbum
 */
class PluginLsgallery_BlockAlbum extends Block
{
    /**
     * Execute
     */
    public function Exec()
    {
        $this->Viewer_Assign("oAlbum", $this->GetParam('oAlbum'));
    }
}
