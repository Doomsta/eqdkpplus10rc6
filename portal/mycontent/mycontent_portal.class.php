<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-05-01 13:28:27 +0200 (Di, 01. Mai 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy_leon $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11769 $
 * 
 * $Id: mycontent_portal.class.php 11769 2012-05-01 11:28:27Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class mycontent_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('core', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'mycontent';
	protected $data		= array(
		'name'			=> 'Custom Content Module',
		'version'		=> '2.0.1',
		'author'		=> 'WalleniuM',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Output a custom content',
	);
	protected $positions = array('middle', 'left1', 'left2', 'right', 'bottom');
	protected $settings	= array(
		'pk_mycontent_headtext'	=> array(
			'name'		=> 'pk_mycontent_headtext',
			'language'	=> 'pk_mycontent_headtext',
			'property'	=> 'text',
			'size'		=> '30',
			'help'		=> '',
		),
		'pk_mycontent_useroutput'	=> array(
			'name'		=> 'pk_mycontent_useroutput',
			'language'	=> 'pk_mycontent_useroutput',
			'property'	=> 'textarea',
			'cols'		=> '40',
			'rows'		=> '8',
			'help'		=> '',
			'codeinput'	=> true,
		),		
	);
	protected $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '7',
	);
	
	protected $multiple = true;

	public function output() {
		if($this->config('pk_mycontent_headtext')){
			$this->header = sanitize($this->config('pk_mycontent_headtext'));
		}
		return html_entity_decode(htmlspecialchars_decode($this->config('pk_mycontent_useroutput')));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_mycontent_portal', mycontent_portal::__shortcuts());
?>