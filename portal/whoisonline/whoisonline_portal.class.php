<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2011-11-01 13:38:39 +0100 (Di, 01. Nov 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy $
 * @copyright	2008-2011 Aderyn
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11419 $
 * 
 * $Id: whoisonline_portal.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class whoisonline_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('pdc');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'whoisonline';
	protected $data		= array(
		'name'			=> 'Online Module',
		'version'		=> '0.1.0',
		'author'		=> 'Aderyn',
		'contact'		=> 'Aderyn@gmx.net',
		'description'	=> 'Show online users',
	);
	protected $positions = array('left1', 'left2', 'right');
	protected $settings	= array(
		'pk_whoisonline_limit'     => array(
			'name'		=> 'wo_limit',
			'language'	=> 'wo_limit',
			'property'	=> 'text',
			'size'		=> '2',
		),
		'pk_whoisonline_dontshowoffline'     => array(
			'name'		=> 'wo_dontshowoffline',
			'language'	=> 'wo_dontshowoffline',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
		)
	);
	protected $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'left2',
		'defaultnumber'		=> '2',
	);

	public function output() {
		include_once($this->root_path.'portal/whoisonline/whoisonline.class.php');
		$class = registry::register('mmo_whoisonline', array(''));
		return $class->getPortalOutput();
	}

	public function reset() {
		$this->pdc->del('portal.module.whoisonline.online'); 
		$this->pdc->del('portal.module.whoisonline.offline');
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_whoisonline_portal', whoisonline_portal::__shortcuts());
?>