<?php

class PluginLsgallery_ModuleNotify extends PluginLsgallery_Inherit_ModuleNotify
{

    /**
     * Отправляет пользователю сообщение о добавлении его в друзья
     *
     * @param ModuleUser_EntityUser $oUserTo
     * @param ModuleUser_EntityUser $oUserFrom
     * @param string $sText
     */
    public function SendUserMarkImageNew(ModuleUser_EntityUser $oUserTo, ModuleUser_EntityUser $oUserFrom, $sText)
    {
        /**
         * Если в конфигураторе указан отложенный метод отправки,
         * то добавляем задание в массив. В противном случае,
         * сразу отсылаем на email
         */
        if (Config::Get('module.notify.delayed')) {
            $oNotifyTask = Engine::GetEntity(
                            'Notify_Task', array(
                        'user_mail' => $oUserTo->getMail(),
                        'user_login' => $oUserTo->getLogin(),
                        'notify_text' => $sText,
                        'notify_subject' => $this->Lang_Get('lsgallery_marked_subject'),
                        'date_created' => date("Y-m-d H:i:s"),
                        'notify_task_status' => self::NOTIFY_TASK_STATUS_NULL,
                            )
            );
            if (Config::Get('module.notify.insert_single')) {
                $this->aTask[] = $oNotifyTask;
            } else {
                $this->oMapper->AddTask($oNotifyTask);
            }
        } else {
            /**
             * Отправляем мыло
             */
            $this->Mail_SetAdress($oUserTo->getMail(), $oUserTo->getLogin());
            $this->Mail_SetSubject($this->Lang_Get('lsgallery_marked_subject'));
            $this->Mail_SetBody($sText);
            $this->Mail_setHTML();
            $this->Mail_Send();
        }
    }

}