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
 * $Id: ventrilo_portal.class.php 11769 2012-05-01 11:28:27Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class ventrilo_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('core', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'ventrilo';
	protected $data		= array(
		'name'			=> 'Ventrilo Status',
		'version'		=> '1.0.0',
		'author'		=> 'Chex',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'A ventrilo status panel',
	);
	protected $positions = array('middle', 'left1', 'left2', 'right', 'bottom');
	protected $settings	= array(
		'pk_ventrilo_server'	=> array(
			'name'		=> 'pk_ventrilo_server',
			'language'	=> 'pk_ventrilo_server',
			'property'	=> 'text',
			'size'		=> '50',
			'help'		=> '',
		),
		'pk_ventrilo_port'	=> array(
			'name'		=> 'pk_ventrilo_port',
			'language'	=> 'pk_ventrilo_port',
			'property'	=> 'text',
			'size'		=> '30',
			'help'		=> '',
		),
		'pk_ventrilo_backgroundc'	=> array(
			'name'		=> 'pk_ventrilo_backgroundc',
			'language'	=> 'pk_ventrilo_backgroundc',
			'property'	=> 'text',
			'size'		=> '6',
			'help'		=> '',
		),  
		'pk_ventrilo_channelc'	=> array(
			'name'		=> 'pk_ventrilo_channelc',
			'language'	=> 'pk_ventrilo_channelc',
			'property'	=> 'text',
			'size'		=> '6',
			'help'		=> '',
		),  
		'pk_ventrilo_servercolor'	=> array(
			'name'		=> 'pk_ventrilo_servercolor',
			'language'	=> 'pk_ventrilo_servercolor',
			'property'	=> 'text',
			'size'		=> '6',
			'help'		=> '',
		),  
		'pk_ventrilo_usercolor'	=> array(
			'name'		=> 'pk_ventrilo_usercolor',
			'language'	=> 'pk_ventrilo_usercolor',
			'property'	=> 'text',
			'size'		=> '6',
			'help'		=> '',
		)
	);
	protected $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '7',
	);

	public function output() {
		return '<iframe src="http://vspy.guildlaunch.net/srv/minispy.php?Address=' . $this->config->get('pk_ventrilo_server') . '&Port=' . $this->config->get('pk_ventrilo_port') . '&J=&Scroll=&T=8&E=&Main=&Color=' . $this->config->get('pk_ventrilo_backgroundc') . '&S=' . $this->config->get('pk_ventrilo_servercolor') . '&C=' . $this->config->get('pk_ventrilo_channelc') . '&U=' . $this->config->get('pk_ventrilo_usercolor') . '&Names=&Compact=" width="200" height="300" frameborder="0"></iframe>';
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ventrilo_portal', ventrilo_portal::__shortcuts());
?>