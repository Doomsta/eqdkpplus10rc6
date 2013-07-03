<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2012-11-11 19:11:09 +0100 (Sun, 11 Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12436 $
 * 
 * $Id: riftspot.class.php 12436 2012-11-11 18:11:09Z wallenium $
 */

include_once('itt_parser.aclass.php');

if(!class_exists('riftspot')) {
	class riftspot extends itt_parser {
		public static $shortcuts = array('pdl', 'puf' => 'urlfetcher', 'pfh' => array('file_handler', array('infotooltips')));

		public $supported_games = array('rift');
		public $av_langs = array();

		public $settings = array();

		public $itemlist = array();
		public $recipelist = array();

		private $searched_langs = array();

		public function __construct($init=false, $config=false, $root_path=false, $cache=false, $puf=false, $pdl=false){
			parent::__construct($init, $config, $root_path, $cache, $puf, $pdl);
			$g_settings = array(
				'rift' => array('icon_loc' => 'http://www.riftspot.com/res/icons/40/', 'icon_ext' => '.png', 'default_icon' => 'unknown'),
			);
			$this->settings = array(
				'itt_icon_loc' => array(	'name' => 'itt_icon_loc',
											'language' => 'pk_itt_icon_loc',
											'fieldtype' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['icon_loc'])) ? $g_settings[$this->config['game']]['icon_loc'] : ''),
				),
				'itt_icon_ext' => array(	'name' => 'itt_icon_ext',
											'language' => 'pk_itt_icon_ext',
											'fieldtype' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['icon_ext'])) ? $g_settings[$this->config['game']]['icon_ext'] : ''),
				),
				'itt_default_icon' => array('name' => 'itt_default_icon',
											'language' => 'pk_itt_default_icon',
											'fieldtype' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['default_icon'])) ? $g_settings[$this->config['game']]['default_icon'] : ''),
				),
			);
			$g_lang = array(
				'rift' => array('en' => 'en_US', 'de' => 'de_DE', 'fr' => 'fr_FR'),
			);
			$this->av_langs = ((isset($g_lang[$this->config['game']])) ? $g_lang[$this->config['game']] : '');
		}

		public function __destruct(){
			unset($this->itemlist);
			unset($this->recipelist);
			unset($this->searched_langs);
			parent::__destruct();
		}


		private function getItemIDfromUrl($itemname, $lang, $searchagain=0){
			$searchagain++;
			$codedname = str_replace(' ', '%2B', $itemname);
			$data = $this->puf->fetch('http://'.(($lang == 'en') ? 'www' : $lang).'.riftspot.com/search?q='. $codedname);
			$this->searched_langs[] = $lang;
			if (preg_match_all('#\[\{\"tpl\":\"items\",\"n\":\"(.*?)\",\"id\":\"items\",\"tclass\":\"default-table\",\"rows\":\[(.*?)\]}\]#', $data, $matchs)) {
				if (preg_match_all('#\{\"id\":\"(.*?)\",\"n\":\"(.*?)\",\"v\":(.*?),\"rl\":([0-9]*),\"rf\":(.*?),\"i\":\"(.*?)\",\"cat\":\{(.*?)\}(.*?)\}#', $matchs[2][0], $matches)) {
					foreach ($matches[0] as $key => $match) {

						$item_name_tosearch = substr(html_entity_decode($matches[2][$key]),1);
						$objJson = json_decode('{"t":"'.$item_name_tosearch.'"}');

						if (strcasecmp($objJson->t, $itemname) == 0) {
							$item_id[0] = $matches[1][$key];
							$item_id[1] = 'items';
							break;
						}
					}
				}
			}

			if(!$item_id[0]) {
				if (preg_match_all('#\[\{\"tpl\":\"recipes\",\"n\":\"(.*?)\",\"id\":\"recipes\",\"rows\":\[(.*?)\]}\]#', $data, $matchs)) {
					if (preg_match_all('#\{\"id\":([0-9]*),\"sl\":([0-9]*),\"r\":([0-9]*),\"rn\":\"(.*?)\",\"p\":\{(.*?)},\"n\":\"(.*?)\",\"comp\":(.*?)\}#', $matchs[2][0], $matches)) {
						foreach ($matches[0] as $key => $match) {
							$item_name_tosearch = substr(html_entity_decode($matches[6][$key]),1);
							if (strcasecmp($item_name_tosearch, $itemname) == 0) {
								$item_id[0] = $matches[1][$key];
								$item_id[1] = 'recipes';
								break;
							}
						}
					}
				}
			}
			if(!$item_id AND count($this->av_langs) > $searchagain) {
				foreach($this->av_langs as $c_lang => $langlong) {
					if(!in_array($c_lang,$this->searched_langs)) {
						$item_id = $this->getItemIDfromUrl($itemname, $c_lang, $searchagain);
					}
					if($item_id[0]) {
						break;
					}
				}
			}
			return $item_id;
		}

		protected function searchItemID($itemname, $lang){
			return $this->getItemIDfromUrl($itemname, $lang);
		}

		protected function getItemData($item_id, $lang, $itemname='', $type='items'){
			$item = array('id' => $item_id);
			if(!$item_id) return null;
			if ($type == 'items') $type = 'item';
			$url = 'http://www.riftspot.com/res/tooltip/'.$this->av_langs[$lang].'/20111212/'.$type.'/js/'.$item['id'].'.js';
			$item['link'] = $url;
			$itemdata = $this->puf->fetch($item['link'], array('Cookie: cookieLangId="'.$lang.'";'));

			if (preg_match('#name:\'(.*?)\', quality:\'(.*?)\', content:\'(.*?)\', icon:\'(.*?)\'#', $itemdata, $matches)){
				$quality = $matches[2];
				$content = $matches[3];
				$icon = $matches[4];
				$template_html = trim(file_get_contents($this->root_path.'infotooltip/includes/parser/templates/riftspot.tpl'));
				$template_html = str_replace('{ITEM_HTML}', $content, $template_html);
				$item['html'] = $template_html;
				$item['lang'] = $lang;
				$item['icon'] = $icon;
				$item['color'] = 'rift_q'.$quality;
				$item['name'] = $matches[1];
			} else {
				$item['baditem'] = true;
			}
			return $item;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_riftspot', riftspot::$shortcuts);
?>