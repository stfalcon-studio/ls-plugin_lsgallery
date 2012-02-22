<?php

class PluginLsgallery_BlockAlbum extends Block
{

    public function Exec()
    {
        $this->Viewer_Assign("oAlbum", $this->GetParam('oAlbum'));
    }

}