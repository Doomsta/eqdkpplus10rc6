<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-10-24 21:37:45 +0200 (Mi, 24. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12326 $
 * 
 * $Id: mmo_news_portal.class.php 12326 2012-10-24 19:37:45Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class mmo_news_portal extends portal_generic {
		public static function __shortcuts() {
		$shortcuts = array();
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'mmo_news';
	protected $data		= array(
		'name'			=> 'MMO-News',
		'version'		=> '0.1.1',
		'author'		=> 'GodMod',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Shows a Module with News for your game',
	);
	protected $positions = array('middle', 'left1', 'left2', 'right');
	protected $settings	= array(
		'pm_mmo_news_count'	=> array(
			'name'		=>	'pm_mmo_news_count',
			'language'	=>	'pm_mmo_news_count',
			'property'	=>	'text',
			'size'		=>	'3',
		),
		'pm_mmo_news_checkURL'	=> array(
			'name'		=>	'pm_mmo_news_checkURL',
			'language'	=>	'pm_mmo_news_checkURL',
			'property'	=>	'checkbox',
		),
	);
	protected $install	= array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'left2',
		'defaultnumber'		=> '5',
	);

	public function output() {
		include_once($this->root_path .'portal/mmo_news/mmo_news_rss.class.php');
		$class = registry::register('mmo_news_rss', array($this->position, $this->id));
		$output = $class->output_left;
		$this->header = sanitize($class->header);
		return $output;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_mmo_news_portal', mmo_news_portal::__shortcuts());
?>