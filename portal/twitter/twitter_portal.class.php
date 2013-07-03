<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-12-14 15:34:31 +0100 (Fr, 14. Dez 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12585 $
 * 
 * $Id: twitter_portal.class.php 12585 2012-12-14 14:34:31Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class twitter_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('pdc', 'config', 'user');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'twitter';
	protected $data		= array(
		'name'			=> 'Twitter-Reader',
		'version'		=> '0.1.0',
		'author'		=> 'GodMod',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Shows a Module with Tweeds',
	);
	protected $positions = array('left1', 'left2', 'right');
	protected $settings	= array(
		'pm_twitter_account'	=> array(
			'name'				=>	'pm_twitter_account',
			'language'			=>	'pm_twitter_account',
			'property'			=>	'text',
			'size'				=>	'30',
		),
		'pm_twitter_maxitems'	=> array(
			'name'				=>	'pm_twitter_maxitems',
			'language'			=>	'pm_twitter_maxitems',
			'property'			=>	'text',
			'size'				=>	'3',
			'default'			=> 3,
		),
		'pm_twitter_cachetime'	=> array(
			'name'				=>	'pm_twitter_cachetime',
			'language'			=>	'pm_twitter_cachetime',
			'property'			=>	'text',
			'size'				=>	'3',
		),
	);
	protected $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'left1',
		'defaultnumber'		=> '1',
	);
	protected $tables	= array('module_twitter');
	protected $sqls		= array(
		'CREATE TABLE IF NOT EXISTS __module_twitter (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`updated` int(11) NOT NULL DEFAULT \'0\',
		`rss` text COLLATE utf8_bin NOT NULL,
		PRIMARY KEY (`id`)
		) DEFAULT CHARSET=utf8 COLLATE=utf8_bin',
	);

	public function output() {	
		include_once($this->root_path . 'portal/twitter/twittermodule.class.php');
		$rss_feeds = registry::register('twittermodule');
		$this->header = $this->user->lang('twitter');
		return $rss_feeds->output_left;
	}

	public function reset() {
		$this->pdc->del('portal.module.twitter');
		$this->db->query("TRUNCATE __module_twitter;");
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_twitter_portal', twitter_portal::__shortcuts());
?>