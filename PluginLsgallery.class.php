<?php

if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

/**
 * Class PluginLsgallery
 */
class PluginLsgallery extends Plugin
{
    /**
     * @var array $aInherits
     */
    public $aInherits = array(
            'action' => array(
                'ActionProfile' => '_ActionProfile',
                'ActionMy'      => '_ActionMy',
                'ActionAdmin'   => '_ActionAdmin',
            ),
            'module' => array(
                'ModuleACL'                   => '_ModuleACL',
                'ModuleRating'                => '_ModuleRating',
                'ModuleNotify'                => '_ModuleNotify',
                'PluginSitemap_ModuleSitemap' => 'PluginLsgallery_ModuleSitemap',
            ),
        );

    /**
     * @var array $aDelegates
     */
    protected $aDelegates  = array(
            'template' => array(
                'menu.album.tpl',
                'menu.album.content.tpl',
                'menu.album_edit.content.tpl',
                'menu.profile.tpl',
            )
        );

    /**
     * Активация плагина
     *
     * @return boolean
     */
    public function Activate()
    {
        $this->Cache_Clean();
        if (!$this->isTableExists('prefix_lsgallery_album')) {
            $this->addEnumType(Config::Get('db.table.comment'), 'target_type', 'image');
            $this->addEnumType(Config::Get('db.table.vote'), 'target_type', 'image');
            $this->addEnumType(Config::Get('db.table.favourite'), 'target_type', 'image');
            $resutls = $this->ExportSQL(dirname(__FILE__) . '/activate.sql');

            return $resutls['result'];
        }

        return true;
    }

    /**
     * Инициализация плагина
     *
     * @return void
     */
    public function Init()
    {
        $this->Viewer_Assign("sTemplateWebPathLsgallery", Plugin::GetTemplateWebPath(__CLASS__));
        $this->Viewer_Assign("sTemplatePathLsgallery", Plugin::GetTemplatePath(__CLASS__));
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('lsgallery') . 'css/gallery-style.css');
        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/jquery.tools.min.js');
        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('lsgallery') . 'js/libs/underscore-min.js', array());
        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('lsgallery') . 'js/common.js');
        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox.pack.js');
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox.css');
        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox-buttons.js');
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('lsgallery') . 'lib/jQuery/plugins/fancybox/jquery.fancybox-buttons.css');
    }

    /**
     * Деактивация плагина
     *
     * @return boolean
     */
    public function Deactivate()
    {
        $this->Cache_Clean();

        return true;
    }
}
