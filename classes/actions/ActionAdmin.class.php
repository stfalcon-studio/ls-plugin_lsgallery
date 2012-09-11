<?php

class PluginLsgallery_ActionAdmin extends PluginLsgallery_Inherit_ActionAdmin
{

    protected function RegisterEvent()
    {
        parent::RegisterEvent();
        $this->AddEvent('recalcimagedata','EventRecalculateImageData');
    }

    protected function EventRecalculateImageData() {
        $this->Security_ValidateSendForm();
        set_time_limit(0);
        $this->PluginLsgallery_Image_RecalculateFavourite();
        $this->PluginLsgallery_Image_RecalculateVote();

        $this->Cache_Clean();

        $this->Message_AddNotice($this->Lang_Get('plugin.lsgallery.lsgallery_admin_images_recalculated'),$this->Lang_Get('attention'));
        $this->SetTemplateAction('index');
    }

}