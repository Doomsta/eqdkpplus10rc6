<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-11-11 19:07:23 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12435 $
 * 
 * $Id: newsarchive_portal.class.php 12435 2012-11-11 18:07:23Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class newsarchive_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'pdh', 'core', 'tpl', 'time', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'newsarchive';
	protected $data		= array(
		'name'			=> 'News-Archive',
		'version'		=> '0.1.0',
		'author'		=> 'GodMod',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Shows a news-archive',
	);
	protected $positions = array('middle', 'left1', 'left2', 'right','bottom');

	protected $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '1',
	);

	public function __construct($position=''){
		parent::__construct($position);

	}

	public function output() {
			$myOut = '';
			if (count($this->pdh->get('news', 'id_list')) > 0){
				$dates = $this->pdh->aget('news', 'date', 0, array($this->pdh->get('news', 'id_list')));
				$cat_count = array();
				foreach($dates as $news_id => $date) {
					$date_array[$this->time->date('Y', $date)][$this->time->date('m', $date)][] = $news_id;
					$cat_id = $this->pdh->get('news', 'category_id', array($news_id));
					$cat_count[$cat_id] = (isset($cat_count[$cat_id])) ? $cat_count[$cat_id] + 1 : 1;
				}
				krsort($date_array);

				//The Side thing for the Months
				$myOut = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="colorswitch">';
				foreach($date_array as $year=>$month){
					krsort($month);
					foreach($month as $key=>$value){
						$myOut .= '<tr><td><a href="'.$this->root_path.'viewnews.php'.$this->SID.'&amp;y='.$year.'&amp;m='.$key.'">'.$this->time->date('F', $this->time->mktime(0,0,0,$key,1,$year)).' '.$year.'</a></td><td>'.count($value).'</td></tr>';
					}
				}
				if ((int)$this->config->get('enable_newscategories')){
					$myOut .= '<tr><th colspan="2">'.$this->user->lang('categories').'</th></tr>';
					//The News-Categorys
					$cats = $this->pdh->sort($this->pdh->get('news_categories', 'id_list'), 'news_categories', 'name');
					foreach($cats as $cat_id) {
						$newscategories[$cat_id] = $this->pdh->get('news_categories', 'category', array($cat_id));
						$myOut .= '<tr><td><a href="'.$this->root_path.'viewnews.php'.$this->SID.'&amp;c='.$cat_id.'">'.sanitize($newscategories[$cat_id]['category_name']).'</a></td><td>'.((isset($cat_count[$cat_id])) ? $cat_count[$cat_id] : 0).'</td></tr>';
					}
				}
				$myOut .= '</table>';
			}
		return $myOut;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_newsarchive_portal', newsarchive_portal::__shortcuts());
?>