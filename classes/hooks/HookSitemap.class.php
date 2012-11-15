<?php

/* -------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
* --------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
* ---------------------------------------------------------
*/

class PluginLsgallery_HookSitemap extends Hook {

	/**
	 * Цепляем обработчики на хуки
	 *
	 * @return void
	 */
	public function RegisterHook() {
		$this->AddHook('sitemap_index_counters', 'SitemapIndex');
	}

	/**
	 * Добавляем ссылку на Sitemap страниц в Sitemap Index
	 *
	 * @param array $aCounters
	 * @return void
	 */
	public function SitemapIndex($aCounters) {
        $aFilter = array(
                'album_type' => array(
                    'open' => true
                ),
                'not_empty' => true
            );
		$aCounters['albums'] = ceil($this->PluginSitemap_Album_GetCountAlbumsByFilter($aFilter) / Config::Get('plugin.sitemap.objects_per_page'));
		$aCounters['photos'] = ceil($this->PluginSitemap_Image_GetCountImages($aFilter) / Config::Get('plugin.sitemap.objects_per_page'));
	}

}