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
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11419 $
 * 
 * $Id: infopages_portal.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class infopages_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('pdh', 'core', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'infopages';
	protected $data		= array(
		'name'			=> 'InfoPages Module',
		'version'		=> '1.0.0',
		'author'		=> 'GodMod',
		'contact'		=> 'godmod@eqdkp-plus.com',
		'description'	=> 'Infopages Menu Block',
	);
	protected $positions = array('middle', 'left1', 'left2', 'right', 'bottom');
	protected $settings	= array(
		'pk_infopages_headtext'	=> array(
			'name'		=> 'pk_infopages_headtext',
			'language'	=> 'pk_infopages_headtext',
			'property'	=> 'text',
			'size'		=> '30',
			'help'		=> '',
		)
	);
	protected $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '1',
	);

	public function output() {
		$returnme = '';
		
		// Set the header
		if($this->config->get('pk_infopages_headtext')){
			$this->header = addslashes($this->config->get('pk_infopages_headtext'));
		}
		$pagelist = $this->pdh->get('pages', 'portalmodule_pages', 0);

		$info_base_url = $this->root_path.'pages.php'.$this->SID.'&amp;page=';
		
		$returnme .= '<ul class="mainmenu">';
		foreach($pagelist as $id => $title){
			$returnme .= '<li>
				<a href="'.$info_base_url.sanitize($id).'" class="copy menu_arrow" target="_top">' . sanitize($title) . '</a>
			</li>';
		}
		$returnme .= '</ul>';
		
		return $returnme;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_infopages_portal', infopages_portal::__shortcuts());
?>