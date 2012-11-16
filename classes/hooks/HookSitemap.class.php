<?php

/**
 * PluginLsgallery_HookSitemap
 *
 * Hooks for generate sitemap
 */
class PluginLsgallery_HookSitemap extends Hook
{

    /**
     * Цепляем обработчики на хуки
     */
    public function RegisterHook()
    {
        $this->AddHook('sitemap_index_counters', 'SitemapIndex');
    }

    /**
     * Добавляем ссылку на Sitemap страниц в Sitemap Index
     *
     * @param array $aCounters
     */
    public function SitemapIndex($aCounters)
    {
        $aFilter = array(
            'album_type' => array(
                'open' => true
            ),
            'not_empty' => true
        );
        $aCounters['albums'] = ceil($this->PluginLsgallery_Album_GetCountAlbumsByFilter($aFilter) / Config::Get('plugin.sitemap.objects_per_page'));
        $aCounters['photos'] = ceil($this->PluginLsgallery_Image_GetCountImagesByFilter($aFilter) / Config::Get('plugin.sitemap.objects_per_page'));
    }

}