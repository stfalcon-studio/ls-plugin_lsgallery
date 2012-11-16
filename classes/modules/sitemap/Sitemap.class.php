<?php

/**
 * Module for plugin Sitemap
 */
class PluginLsgallery_ModuleSitemap extends PluginLsgallery_Inherit_PluginSitemap_ModuleSitemap {

	/**
	 * Change data for Sitemap Index
	 *
	 * @return array
	 */
	public function getExternalCounters() {
		$aCounters = parent::getExternalCounters();
        $aFilter = array(
                'album_type' => array(
                    'open' => true
                ),
                'not_empty' => true
            );
		$aCounters['albums'] = ceil($this->PluginLsgallery_Album_GetCountAlbumsByFilter($aFilter) / Config::Get('plugin.sitemap.objects_per_page'));
		$aCounters['images'] = ceil($this->PluginLsgallery_Image_GetCountImagesByFilter($aFilter) / Config::Get('plugin.sitemap.objects_per_page'));
		return $aCounters;

	}

	/**
	 * Get data for static albums pages Sitemap
	 *
	 * @param integer $iCurrPage
	 * @return array
	 */
	public function GetDataForAlbums($iCurrPage) {
		$iPerPage = Config::Get('plugin.sitemap.objects_per_page');
		$sCacheKey = "sitemap_albums_{$iCurrPage}_" . $iPerPage;

		if (false === ($aData = $this->Cache_Get($sCacheKey))) {
			$aFilter = array(
                'album_type' => array(
                    'open' => true
                ),
                'not_empty' => true
            );
			$aAlbums = $this->PluginLsgallery_Album_GetAlbumsByFilter($aFilter, $iCurrPage, $iPerPage);
			$aData = array();
			foreach ($aAlbums['collection'] as $oAlbum) {
				$aData[] = $this->PluginSitemap_Sitemap_GetDataForSitemapRow(
					$oAlbum->getUrlFull(),
					$oAlbum->getDateModified(),
					Config::Get('sitemap.sitemap_priority'),
					Config::Get('sitemap.sitemap_changefreq')
				);
			}

			$this->Cache_Set($aData, $sCacheKey, array('page_change'), Config::Get('plugin.lsgallery.sitemap.cache_lifetime'));
		}

		return $aData;
	}


    /**
	 * Get data for static images pages Sitemap
	 *
	 * @param integer $iCurrPage
	 * @return array
	 */
	public function GetDataForImages($iCurrPage) {
		$iPerPage = Config::Get('plugin.sitemap.objects_per_page');
		$sCacheKey = "sitemap_images_{$iCurrPage}_" . $iPerPage;
		if (false === ($aData = $this->Cache_Get($sCacheKey))) {
            $aFilter = array(
                'album_type' => array(
                    'open' => true
                ),
                'not_empty' => true
            );

			$aImages = $this->PluginLsgallery_Image_GetImagesByFilter($aFilter, $iCurrPage, $iPerPage);
			$aData = array();
			foreach ($aImages['collection'] as $oImage) {
				$aData[] = $this->PluginSitemap_Sitemap_GetDataForSitemapRow(
					$oImage->getUrlFull(),
					$oImage->getDateModified(),
					Config::Get('sitemap.sitemap_priority'),
					Config::Get('sitemap.sitemap_changefreq')
				);
			}

			$this->Cache_Set($aData, $sCacheKey, array('page_change'), Config::Get('plugin.lsgallery.sitemap.cache_lifetime'));
		}

		return $aData;
	}
}
