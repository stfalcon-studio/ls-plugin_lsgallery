<?php

class PluginLsgallery_ActionGallery extends ActionPlugin
{

    /**
     * @var ModuleUser_EntityUser
     */
    protected $oUserCurrent = null;
    protected $sMenuHeadItemSelect = 'gallery';
    protected $sMenuItemSelect = 'image';
    protected $sMenuSubItemSelect = 'all';

    /**
     * Action initiaization
     */
    public function Init()
    {
        $this->oUserCurrent = $this->User_GetUserCurrent();
        $this->Lang_AddLangJs(array(
            'plugin.lsgallery.lsgallery_images_upload_choose',
            'plugin.lsgallery.lsgallery_album_image_delete',
            'plugin.lsgallery.lsgallery_album_set_image_cover',
            'plugin.lsgallery.lsgallery_album_image_cover',
            'plugin.lsgallery.lsgallery_album_image_delete_confirm',
            'plugin.lsgallery.lsgallery_image_mark_cancel',
            'plugin.lsgallery.lsgallery_image_tags',
            'plugin.lsgallery.lsgallery_image_tags_updated',
            'plugin.lsgallery.lsgallery_image_description',
            'plugin.lsgallery.lsgallery_image_description_updated',
            'plugin.lsgallery.lsgallery_save',
            'plugin.lsgallery.lsgallery_image_move_album'
        ));
        $this->SetDefaultEvent('photo');
    }

    /**
     * Register routes
     */
    protected function RegisterEvent()
    {
        $this->AddEvent('create', 'EventCreateAlbum');
        $this->AddEvent('update', 'EventUpdateAlbum');
        $this->AddEvent('delete', 'EventDeleteAlbum');
        $this->AddEvent('admin-images', 'EventAdminImages');

        $this->AddEvent('image', 'EventViewImage');
        $this->AddEvent('album', 'EventViewAlbum');
        $this->AddEvent('albums', 'EventViewGallery');

        $this->AddEvent('tag', 'EventTag');

        $this->AddEvent('photo', 'EventPhoto');

        $this->AddEvent('ajaxaddcomment', 'AjaxAddComment');
        $this->AddEvent('ajaxresponsecomment', 'AjaxResponseComment');
    }

    public function EventPhoto()
    {
        $sType = $this->GetParam(0, 'main');
        $sOrder = getRequest("order", "desc", 'get');
        $this->Viewer_Assign('sOrder', $sOrder);

        switch ($sType) {
            case 'main':
                $this->EventMainPhoto();
                return;
                break;
            case 'new':
                $this->EventListPhoto($sType);
                return;
                break;
            case 'best':
                $this->EventListPhoto($sType);
                return;
                break;
            default:
                $this->EventMainPhoto();
                return;
                break;
        }
    }

    protected function EventMainPhoto()
    {
        $this->sMenuItemSelect = 'image';
        $this->sMenuSubItemSelect = 'main';

        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.lsgallery.lsgallery_photo_day'));
        $this->SetTemplateAction('photo');

        $oImage = $this->PluginLsgallery_Image_GetImageOfDay();
        $aRandomImages = $this->PluginLsgallery_Image_GetRandomImages(Config::Get('plugin.lsgallery.images_random'));
        $aResult = $this->PluginLsgallery_Album_GetAlbumsIndex(1, Config::Get('plugin.lsgallery.album_block'));

        $this->Viewer_Assign('oImage', $oImage);
        $this->Viewer_Assign('oAlbum', $oImage?$oImage->getAlbum():null);
        $this->Viewer_Assign('aAlbums', $aResult['collection']);
        $this->Viewer_Assign('aRandomImages', $aRandomImages);

        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox.pack.js');
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox.css');
    }

    protected function EventListPhoto($sType)
    {
        $this->sMenuItemSelect = 'image';
        $this->sMenuSubItemSelect = $sType;

        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.lsgallery.lsgallery_photo_' . $sType));
        $this->SetTemplateAction('photos');

        $iPage = $this->_getPage();

        if ($sType == 'new') {
            $aResult = $this->PluginLsgallery_Image_GetImagesNew($iPage, Config::Get('plugin.lsgallery.image_per_page'));
        } elseif ($sType == 'best') {
            $aResult = $this->PluginLsgallery_Image_GetImagesBest($iPage, Config::Get('plugin.lsgallery.image_per_page'));
        }

        $aImages = $aResult['collection'];

        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('plugin.lsgallery.image_per_page'), 4, Router::GetPath('gallery') . 'photo/' . $sType);

        $this->Viewer_Assign('aImages', $aImages);
        $this->Viewer_Assign('aPaging', $aPaging);
    }

    /**
     * Create album
     */
    public function EventCreateAlbum()
    {
        $this->sMenuItemSelect = 'create';

        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.lsgallery.lsgallery_create_album_title'));

        if (!$this->ACL_AllowCreateAlbum($this->oUserCurrent)) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return Router::Action('error');
        }

        if (!$this->ACL_CanCreateAlbum($this->oUserCurrent)) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.lsgallery.lsgallery_albums_no_rating'), $this->Lang_Get('error'));
            return Router::Action('error');
        }

        $this->SetTemplateAction('create');
        $this->Viewer_Assign('aLocalizedTypes', PluginLsgallery_ModuleAlbum_EntityAlbum::getLocalizedTypes($this));

        if (isPost('submit_create_album')) {
            if (!$this->_checkAlbumFields()) {
                return;
            }

            $oAlbum = Engine::GetEntity('PluginLsgallery_ModuleAlbum_EntityAlbum');
            $oAlbum->setUserId($this->oUserCurrent->getId());
            $oAlbum->setTitle(getRequest('album_title'));
            $oAlbum->setDescription(getRequest('album_description'));
            $oAlbum->setType(getRequest('album_type'));

            $this->Hook_Run('gallery_album_add_before', array('oAlbum' => $oAlbum));
            if ($this->PluginLsgallery_Album_CreateAlbum($oAlbum)) {
                $this->Hook_Run('gallery_album_add_after', array('oAlbum' => $oAlbum));
                Router::Location($oAlbum->getUrlFull('images'));
            } else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                return Router::Action('error');
            }
        }
    }

    /**
     *  Delete album
     */
    public function EventDeleteAlbum()
    {
        $this->Security_ValidateSendForm();

        $sId = $this->GetParam(0);

        if (!$oAlbum = $this->PluginLsgallery_Album_GetAlbumById($sId)) {
            return $this->EventNotFound();
        }
        if (!$this->ACL_AllowDeleteAlbum($this->oUserCurrent, $oAlbum)) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return Router::Action('error');
        }

        if ($this->PluginLsgallery_Album_DeleteAlbum($oAlbum)) {
            $this->Message_AddNoticeSingle($this->Lang_Get('plugin.lsgallery.lsgallery_delete_album_success'), $this->Lang_Get('attention'), true);
            Router::Location(Router::GetPath('gallery'));
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            Router::Location($oAlbum->getUrlFull());
        }
    }

    /**
     * Update album
     */
    public function EventUpdateAlbum()
    {
        $this->sMenuItemSelect = 'album';
        $this->sMenuSubItemSelect = 'update';

        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.lsgallery.lsgallery_update_album_title'));

        $sId = $this->GetParam(0);

        if (!$oAlbum = $this->PluginLsgallery_Album_GetAlbumById($sId)) {
            return $this->EventNotFound();
        }
        if (!$this->ACL_AllowUpdateAlbum($this->oUserCurrent, $oAlbum)) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return Router::Action('error');
        }

        $this->SetTemplateAction('create');
        $this->Viewer_Assign('aLocalizedTypes', PluginLsgallery_ModuleAlbum_EntityAlbum::getLocalizedTypes($this));
        $this->Viewer_Assign('oAlbumEdit', $oAlbum);

        if (isPost('submit_create_album')) {
            if (!$this->_checkAlbumFields($oAlbum)) {
                return;
            }

            $oAlbum->setTitle(getRequest('album_title'));
            $oAlbum->setDescription(getRequest('album_description'));
            $oAlbum->setType(getRequest('album_type'));


            if ($this->PluginLsgallery_Album_UpdateAlbum($oAlbum)) {
                Router::Location($oAlbum->getUrlFull());
            } else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                return Router::Action('error');
            }
        } else {
            $_REQUEST['album_id'] = $oAlbum->getId();
            $_REQUEST['album_title'] = $oAlbum->getTitle();
            $_REQUEST['album_description'] = $oAlbum->getDescription();
            $_REQUEST['album_type'] = $oAlbum->getType();
        }
    }

    /**
     * Admin images
     */
    public function EventAdminImages()
    {
        $sId = $this->GetParam(0);
        /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
        if (!$oAlbum = $this->PluginLsgallery_Album_GetAlbumById($sId)) {
            return $this->EventNotFound();
        }
        if (!$this->ACL_AllowAdminAlbumImages($this->oUserCurrent, $oAlbum)) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return Router::Action('error');
        }

        $this->sMenuItemSelect = 'album';
        $this->sMenuSubItemSelect = 'admin-images';

        $iPage = $this->_getPage();
        $aResult = $this->PluginLsgallery_Image_GetImagesByAlbumId($oAlbum->getId(), $iPage, Config::Get('plugin.lsgallery.image_per_page'));
        $aImages = $aResult['collection'];

        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('plugin.lsgallery.image_per_page'), 4, rtrim($oAlbum->getUrlFull('images'), '/'));

        $aResult = $this->PluginLsgallery_Album_GetAlbumsPersonalByUser($this->oUserCurrent->getId());
        $aAlbums = $aResult['collection'];
        unset($aAlbums[$oAlbum->getId()]);

        $this->Hook_Run('gallery_admin_image', array('oAlbum' => $oAlbum));
        
        $this->Viewer_AddHtmlTitle($oAlbum->getTitle());
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.lsgallery.lsgallery_control_album'));

        $this->Viewer_Assign('aImages', $aImages);
        $this->Viewer_Assign('oAlbumEdit', $oAlbum);
        $this->Viewer_Assign('aPaging', $aPaging);
        $this->Viewer_Assign('aAlbums', $aAlbums);

    }

    public function EventViewImage()
    {
        $sId = $this->GetParam(0);
        $sOrder = getRequest("order", "desc", 'get');
        if (!in_array(strtolower($sOrder), array('asc', 'desc'))){
            $sOrder = 'desc';
        }

        /* @var $oImage PluginLsgallery_ModuleImage_EntityImage */
        if (!$oImage = $this->PluginLsgallery_Image_GetImageById($sId)) {
            return $this->EventNotFound();
        }
        /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
        if (!$oAlbum = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId())) {
            return $this->EventNotFound();
        }

        if (!$this->ACL_AllowViewAlbumImages($this->oUserCurrent, $oAlbum)) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return Router::Action('error');
        }

        if (getRequest('submit_comment')) {
            $this->SubmitComment();
        }

        if (!Config::Get('module.comment.nested_page_reverse') and Config::Get('module.comment.use_nested') and Config::Get('module.comment.nested_per_page')) {
            $iPageDef = ceil($this->Comment_GetCountCommentsRootByTargetId($oImage->getId(), 'image') / Config::Get('module.comment.nested_per_page'));
        } else {
            $iPageDef = 1;
        }

        $iPage = getRequest('cmtpage', 0) ? (int) getRequest('cmtpage', 0) : $iPageDef;
        $aReturn = $this->Comment_GetCommentsByTargetId($oImage->getId(), 'image', $iPage, Config::Get('module.comment.nested_per_page'));
        $iMaxIdComment = $aReturn['iMaxIdComment'];
        $aComments = $aReturn['comments'];
        if (Config::Get('module.comment.use_nested') and Config::Get('module.comment.nested_per_page')) {
            $aPaging = $this->Viewer_MakePaging($aReturn['count'], $iPage, Config::Get('module.comment.nested_per_page'), 4, '');
            if (!Config::Get('module.comment.nested_page_reverse') and $aPaging) {
                // переворачиваем страницы в обратном порядке
                $aPaging['aPagesLeft'] = array_reverse($aPaging['aPagesLeft']);
                $aPaging['aPagesRight'] = array_reverse($aPaging['aPagesRight']);
            }
            $this->Viewer_Assign('aPagingCmt', $aPaging);
        }

        if ($this->oUserCurrent) {
            $oImageRead = Engine::GetEntity('PluginLsgallery_ModuleImage_EntityImageRead');
            $oImageRead->setImageId($oImage->getId());
            $oImageRead->setUserId($this->oUserCurrent->getId());
            $oImageRead->setCommentCountLast($oImage->getCountComment());
            $oImageRead->setCommentIdLast($iMaxIdComment);
            $oImageRead->setDateRead();
            $this->PluginLsgallery_Image_SetImageRead($oImageRead);

        }


        $this->Hook_Run('gallery_view_image', array('oUser' => $this->oUserCurrent, 'oImage' => $oImage, 'oAlbum' => $oAlbum));

        $this->SetTemplateAction('view');

        $oPrevImage = $this->PluginLsgallery_Image_GetPrevImage($oImage, $sOrder);
        $oNextImage = $this->PluginLsgallery_Image_GetNextImage($oImage, $sOrder);

        $this->Viewer_AddHtmlTitle($oAlbum->getTitle());
        $this->Viewer_SetHtmlDescription($oImage->getDescription());
        $this->Viewer_SetHtmlKeywords($oImage->getImageTags());

        $this->Viewer_Assign('oImage', $oImage);
        $this->Viewer_Assign('oAlbum', $oAlbum);
        $this->Viewer_Assign('oPrevImage', $oPrevImage);
        $this->Viewer_Assign('oNextImage', $oNextImage);
        $this->Viewer_Assign('sOrder', $sOrder);

        $this->Viewer_Assign('aComments', $aComments);
        $this->Viewer_Assign('iMaxIdComment', $iMaxIdComment);

        $this->Viewer_AddBlock('right', 'Album', array('plugin' => 'lsgallery', 'oAlbum' => $oAlbum), Config::Get('plugin.lsgallery.priority_album_block'));

        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox.pack.js');
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox.css');
    }

    public function EventViewAlbum()
    {
        $sId = $this->GetParam(0);
        $sOrder = getRequest("order", "desc", 'get');

        $aOrderPermission = array('asc', 'desc');
        if (!in_array($sOrder, $aOrderPermission)) {
            $sOrder = 'desc';
        }

        /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
        if (!$oAlbum = $this->PluginLsgallery_Album_GetAlbumById($sId)) {
            return $this->EventNotFound();
        }

        if (!$this->ACL_AllowViewAlbumImages($this->oUserCurrent, $oAlbum)) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return Router::Action('error');
        }

        $this->Hook_Run('gallery_album_show', array('oAlbum' => $oAlbum));

        $this->sMenuItemSelect = 'album';

        if ($this->oUserCurrent && $this->oUserCurrent->getId() == $oAlbum->getUserId()) {
            $this->sMenuSubItemSelect = 'my';
        } else {
            $this->sMenuSubItemSelect = 'all';
        }

        $this->SetTemplateAction('album');

        $iPage = $this->_getPage();
        $aResult = $this->PluginLsgallery_Image_GetImagesByAlbumId($oAlbum->getId(), $iPage, Config::Get('plugin.lsgallery.image_per_page'), $sOrder);
        $aImages = $aResult['collection'];

        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('plugin.lsgallery.image_per_page'), 4, rtrim($oAlbum->getUrlFull() . '/' . $sOrder, '/'));

        $this->Viewer_AddHtmlTitle($oAlbum->getTitle());

        $this->Viewer_Assign('oAlbum', $oAlbum);
        $this->Viewer_Assign('aImages', $aImages);
        $this->Viewer_Assign('aPaging', $aPaging);
        $this->Viewer_Assign('sOrder', $sOrder);
        if ($sOrder == "desc"){
            $sOrderLink = "asc";
        } else {
            $sOrderLink = "desc";
        }
        $this->Viewer_Assign('sOrderLink', $sOrderLink);

        $this->Viewer_AddBlock('right', 'Album', array('plugin' => 'lsgallery', 'oAlbum' => $oAlbum), Config::Get('plugin.lsgallery.priority_album_block'));

        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox.pack.js');
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox.css');

        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox-buttons.js');
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox-buttons.css');


    }

    public function EventViewGallery()
    {

        $this->sMenuItemSelect = 'album';

        $this->SetTemplateAction('gallery');

        $iPage = $this->_getPage();
        $aResult = $this->PluginLsgallery_Album_GetAlbumsIndex($iPage, Config::Get('plugin.lsgallery.album_per_page'));
        $aAlbums = $aResult['collection'];

        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('plugin.lsgallery.album_per_page'), 4, Router::GetPath('gallery') . 'albums/');

        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.lsgallery.lsgallery_title_albums'));

        $this->Viewer_Assign('aAlbums', $aAlbums);
        $this->Viewer_Assign('aPaging', $aPaging);
    }

    public function EventTag()
    {
        $sTag = $this->GetParam(0);

        if (!$sTag) {
            return $this->EventNotFound();
        }
        /**
         * Передан ли номер страницы
         */
        $iPage = $this->_getPage();
        /**
         * Получаем список топиков
         */
        $aResult = $this->PluginLsgallery_Image_GetImagesByTag($sTag, $iPage, Config::Get('plugin.lsgallery.image_per_page'));
        $aImage = $aResult['collection'];
        /**
         * Формируем постраничность
         */
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('plugin.lsgallery.image_per_page'), 4, Router::GetPath('gallery') . 'tag/' . htmlspecialchars($sTag));
        /**
         * Загружаем переменные в шаблон
         */
        $this->Viewer_Assign('aPaging', $aPaging);
        $this->Viewer_Assign('aImage', $aImage);
        $this->Viewer_Assign('sTag', $sTag);
        $this->Viewer_AddHtmlTitle($this->Lang_Get('tag_title'));
        $this->Viewer_AddHtmlTitle($sTag);
    }

    protected function _getPage()
    {
        $iPage = 1;
        foreach ($this->GetParams() as $sParam) {
            if (preg_match('/^page(\d+)?$/i', $sParam, $matches)) {
                if (isset($matches[1])) {
                    $iPage = $matches[1];
                }
            }
        }

        return $iPage;
    }

    protected function AjaxAddComment()
    {
        $this->Viewer_SetResponseAjax('json');
        $this->SubmitComment();
    }

    /**
     * Обработка добавление комментария
     *
     * @return bool
     */
    protected function SubmitComment()
    {
        /**
         * Проверям авторизован ли пользователь
         */
        if (!$this->User_IsAuthorization()) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }

        /* @var $oImage PluginLsgallery_ModuleImage_EntityImage */
        if (!($oImage = $this->PluginLsgallery_Image_GetImageById(getRequest('cmt_target_id')))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }
        /* @var $oAlbum PluginLsgallery_ModuleAlbum_EntityAlbum */
        if (!$oAlbum = $this->PluginLsgallery_Album_GetAlbumById($oImage->getAlbumId())) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }

        if (!$this->ACL_AllowViewAlbumImages($this->oUserCurrent, $oAlbum)) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }

        /**
         * Проверяем разрешено ли постить комменты
         */
        if (!$this->ACL_CanPostComment($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_acl'), $this->Lang_Get('error'));
            return;
        }
        /**
         * Проверяем разрешено ли постить комменты по времени
         */
        if (!$this->ACL_CanPostCommentTime($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_limit'), $this->Lang_Get('error'));
            return;
        }

        /**
         * Проверяем текст комментария
         */
        $sText = $this->Text_Parser(getRequest('comment_text'));
        if (!func_check($sText, 'text', 2, 10000)) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_add_text_error'), $this->Lang_Get('error'));
            return;
        }
        /**
         * Проверям на какой коммент отвечаем
         */
        $sParentId = (int) getRequest('reply');
        if (!func_check($sParentId, 'id')) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }
        $oCommentParent = null;
        if ($sParentId != 0) {
            /**
             * Проверяем существует ли комментарий на который отвечаем
             */
            if (!($oCommentParent = $this->Comment_GetCommentById($sParentId))) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                return;
            }
            /**
             * Проверяем из одного топика ли новый коммент и тот на который отвечаем
             */
            if ($oCommentParent->getTargetId() != $oImage->getId()) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                return;
            }
        } else {
            /**
             * Корневой комментарий
             */
            $sParentId = null;
        }
        /**
         * Проверка на дублирующий коммент
         */
        if ($this->Comment_GetCommentUnique($oImage->getId(), 'image', $this->oUserCurrent->getId(), $sParentId, md5($sText))) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_spam'), $this->Lang_Get('error'));
            return;
        }
        /**
         * Создаём коммент
         */
        $oCommentNew = Engine::GetEntity('Comment');
        $oCommentNew->setTargetId($oImage->getId());
        $oCommentNew->setTargetType('image');
        $oCommentNew->setTargetParentId($oImage->getAlbumId());
        $oCommentNew->setUserId($this->oUserCurrent->getId());
        $oCommentNew->setText($sText);
        $oCommentNew->setDate(date("Y-m-d H:i:s"));
        $oCommentNew->setUserIp(func_getIp());
        $oCommentNew->setPid($sParentId);
        $oCommentNew->setTextHash(md5($sText));
        $oCommentNew->setPublish(1);

        /**
         * Добавляем коммент
         */
        $this->Hook_Run('image_comment_add_before', array('oCommentNew' => $oCommentNew, 'oCommentParent' => $oCommentParent, 'oImage' => $oImage));
        if ($this->Comment_AddComment($oCommentNew)) {
            $this->Hook_Run('image_comment_add_after', array('oCommentNew' => $oCommentNew, 'oCommentParent' => $oCommentParent, 'oImage' => $oImage));

            $this->Viewer_AssignAjax('sCommentId', $oCommentNew->getId());

            $this->PluginLsgallery_Image_IncreaseImageCountComment($oCommentNew->getTargetId());

            $this->oUserCurrent->setDateCommentLast(date("Y-m-d H:i:s"));
            $this->User_Update($this->oUserCurrent);
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
        }
    }

    /**
     * Получение новых комментариев
     *
     */
    protected function AjaxResponseComment()
    {
        $this->Viewer_SetResponseAjax('json');

        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }

        $idImage = getRequest('idTarget', null, 'post');
        if (!($oImage = $this->PluginLsgallery_Image_GetImageById($idImage))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }

        $idCommentLast = getRequest('idCommentLast', null, 'post');
        $selfIdComment = getRequest('selfIdComment', null, 'post');
        $aComments = array();

        if (getRequest('bUsePaging', null, 'post') && $selfIdComment) {
            $oComment = $this->Comment_GetCommentById($selfIdComment);
            if ($oComment && $oComment->getTargetId() == $oImage->getId() && $oComment->getTargetType() == 'image') {
                $oViewerLocal = $this->Viewer_GetLocalViewer();
                $oViewerLocal->Assign('oUserCurrent', $this->oUserCurrent);
                $oViewerLocal->Assign('bOneComment', true);

                $oViewerLocal->Assign('oComment', $oComment);
                $sText = $oViewerLocal->Fetch("comment.tpl");
                $aCmt = array();
                $aCmt[] = array(
                    'html' => $sText,
                    'obj' => $oComment,
                );
            } else {
                $aCmt = array();
            }
            $aReturn['comments'] = $aCmt;
            $aReturn['iMaxIdComment'] = $selfIdComment;
        } else {
            $aReturn = $this->Comment_GetCommentsNewByTargetId($oImage->getId(), 'image', $idCommentLast);
        }
        $iMaxIdComment = $aReturn['iMaxIdComment'];

        $oImageRead = Engine::GetEntity('PluginLsgallery_ModuleImage_EntityImageRead');
        $oImageRead->setImageId($oImage->getId());
        $oImageRead->setUserId($this->oUserCurrent->getId());
        $oImageRead->setCommentCountLast($oImage->getCountComment());
        $oImageRead->setCommentIdLast($iMaxIdComment);
        $oImageRead->setDateRead();
        $this->PluginLsgallery_Image_SetImageRead($oImageRead);

        $aCmts = $aReturn['comments'];
        if ($aCmts and is_array($aCmts)) {
            foreach ($aCmts as $aCmt) {
                $aComments[] = array(
                    'html' => $aCmt['html'],
                    'idParent' => $aCmt['obj']->getPid(),
                    'id' => $aCmt['obj']->getId(),
                );
            }
        }

        $this->Viewer_AssignAjax('iMaxIdComment', $iMaxIdComment);
        $this->Viewer_AssignAjax('aComments', $aComments);
    }

    /**
     * Validate album fields
     *
     * @param PluginLsgallery_ModuleAlbum_EntityAlbum $oAlbum
     * @return boolean
     */
    protected function _checkAlbumFields($oAlbum = null)
    {
        $this->Security_ValidateSendForm();
        $bOk = true;

        if (!is_null($oAlbum)) {
            if ($oAlbum->getId() != getRequest('album_id')) {
                $this->Message_AddError($this->Lang_Get('plugin.lsgallery.lsgallery_album_id_error'), $this->Lang_Get('error'));
                $bOk = false;
            }
            if ($oAlbum->getType() == $oAlbum::TYPE_SHARED && getRequest('album_type') != $oAlbum::TYPE_SHARED) {
                $this->Message_AddError($this->Lang_Get('plugin.lsgallery.lsgallery_album_type_error'), $this->Lang_Get('error'));
                $bOk = false;
            }
        }

        if (!func_check(getRequest('album_title'), 'text', 2, 64)) {
            $this->Message_AddError($this->Lang_Get('plugin.lsgallery.lsgallery_album_title_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        $sDescription = getRequest('album_description');
        if ($sDescription && !func_check($sDescription, 'text', 10, 512)) {
            $this->Message_AddError($this->Lang_Get('plugin.lsgallery.lsgallery_album_description_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        $aTypes = PluginLsgallery_ModuleAlbum_EntityAlbum::getLocalizedTypes($this);

        if (!in_array(getRequest('album_type'), array_keys($aTypes))) {
            $this->Message_AddError($this->Lang_Get('plugin.lsgallery.lsgallery_album_type_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        return $bOk;
    }

    public function EventShutdown()
    {
        $this->Viewer_Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
        $this->Viewer_Assign('sMenuItemSelect', $this->sMenuItemSelect);
        $this->Viewer_Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);

        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('lsgallery') . 'js/gallery.js');
    }

}
