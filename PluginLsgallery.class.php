<?php

if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginLsgallery extends Plugin
{

    public $aInherits = array(
        'action' => array(
            'ActionProfile' => '_ActionProfile',
            'ActionMy' => '_ActionMy',
            'ActionAdmin' => '_ActionAdmin',
        ),
        'module' => array(
            'ModuleACL' => '_ModuleACL',
            'ModuleRating' => '_ModuleRating',
            'ModuleUser' => '_ModuleUser',
            'ModuleNotify' => '_ModuleNotify',
            'PluginSitemap_ModuleSitemap' => 'PluginLsgallery_ModuleSitemap',
        ),
        'mapper' => array(
            'ModuleUser_MapperUser' => '_ModuleUser_MapperUser'
        ),
    );
    protected $aDelegates = array(
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
        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('lsgallery') . 'js/common.js');
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