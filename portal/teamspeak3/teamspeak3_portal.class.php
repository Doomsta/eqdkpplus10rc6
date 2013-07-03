<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-10-22 20:15:33 +0200 (Mo, 22. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12321 $
 * 
 * $Id: teamspeak3_portal.class.php 12321 2012-10-22 18:15:33Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class teamspeak3_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('core', 'pdc', 'config', 'tpl');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'teamspeak3';
	protected $data		= array(
		'name'			=> 'Teamspeak3 Module',
		'version'		=> '1.0.3',
		'author'		=> 'Sylna',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Teamspeak3 Server Informationt',
	);
	protected $positions = array('left1', 'left2', 'right');
	protected $settings	= array(
		'pk_ts3_ip'		=> array(
			'name'		=> 'pk_ts3_ip',
			'language'	=> 'lang_pk_ts3_ip',
			'property'	=> 'text',
			'size'		=> '15',
			'help'		=> '',
		),
		'pk_ts3_port'		=> array(
			'name'		=> 'pk_ts3_port',
			'language'	=> 'lang_pk_ts3_port',
			'property'	=> 'text',
			'size'		=> '5',
			'help'		=> '',
		),
		'pk_ts3_telnetport'		=> array(
			'name'		=> 'pk_ts3_telnetport',
			'language'	=> 'lang_pk_ts3_telnetport',
			'property'	=> 'text',
			'size'		=> '5',
			'help'		=> '',
		),
		'pk_ts3_id'		=> array(
			'name'		=> 'pk_ts3_id',
			'language'	=> 'lang_pk_ts3_id',
			'property'	=> 'text',
			'size'		=> '2',
			'help'		=> 'lang_help_pk_ts3_id',
		),
		'pk_ts3_cache'		=> array(
			'name'		=> 'pk_ts3_cache',
			'language'	=> 'lang_pk_ts3_cache',
			'property'	=> 'text',
			'size'		=> '2',
			'help'		=> 'lang_help_pk_ts3_cache',
		),
		'pk_ts3_banner'		=> array(
			'name'		=> 'pk_ts3_banner',
			'language'	=> 'lang_pk_ts3_banner',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
		'pk_ts3_join'		=> array(
			'name'		=> 'pk_ts3_join',
			'language'	=> 'lang_pk_ts3_join',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
		'pk_ts3_jointext'		=> array(
			'name'		=> 'pk_ts3_jointext',
			'language'	=> 'lang_pk_ts3_jointext',
			'property'	=> 'text',
			'size'		=> '30',
			'help'		=> '',
		),
		'pk_ts3_legend'		=> array(
			'name'		=> 'pk_ts3_legend',
			'language'	=> 'lang_pk_ts3_legend',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
		'pk_ts3_cut_names'		=> array(
			'name'		=> 'pk_ts3_cut_names',
			'language'	=> 'lang_pk_ts3_cut_names',
			'property'	=> 'text',
			'size'		=> '2',
			'help'		=> 'lang_help_pk_ts3_cut_names',
		),
		'pk_ts3_cut_channel'		=> array(
			'name'		=> 'pk_ts3_cut_channel',
			'language'	=> 'lang_pk_ts3_cut_channel',
			'property'	=> 'text',
			'size'		=> '2',
			'help'		=> 'lang_help_pk_ts3_cut_channel',
		),
		'pk_only_populated_channel'		=> array(
			'name'		=> 'pk_only_populated_channel',
			'language'	=> 'lang_pk_only_populated_channel',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
		'pk_ts3_useron'		=> array(
			'name'		=> 'pk_ts3_useron',
			'language'	=> 'lang_pk_ts3_useron',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
		'pk_ts3_stats'		=> array(
			'name'		=> 'pk_ts3_stats',
			'language'	=> 'lang_pk_ts3_stats',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
		'pk_ts3_stats_showos'		=> array(
			'name'		=> 'pk_ts3_stats_showos',
			'language'	=> 'lang_pk_ts3_stats_showos',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
		'pk_ts3_stats_version'		=> array(
			'name'		=> 'pk_ts3_stats_version',
			'language'	=> 'lang_pk_ts3_stats_version',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
		'pk_ts3_stats_numchan'		=> array(
			'name'		=> 'pk_ts3_stats_numchan',
			'language'	=> 'lang_pk_ts3_stats_numchan',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
		'pk_ts3_stats_uptime'		=> array(
			'name'		=> 'pk_ts3_stats_uptime',
			'language'	=> 'lang_pk_ts3_stats_uptime',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
		'pk_ts3_stats_install'		=> array(
			'name'		=> 'pk_ts3_stats_install',
			'language'	=> 'lang_pk_ts3_stats_install',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
		'pk_ts3_timeout'		=> array(
			'name'		=> 'pk_ts3_timeout',
			'language'	=> 'lang_pk_ts3_timeout',
			'property'	=> 'text',
			'size'		=> '20',
			'help'		=> 'lang_help_pk_ts3_timeout',
		),
		'pk_ts3_hide_spacer'		=> array(
			'name'		=> 'pk_ts3_hide_spacer',
			'language'	=> 'lang_pk_ts3_hide_spacer',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> '',
		),
	);
	protected $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'left2',
		'defaultnumber'		=> '15',
	);

	public function output() {
		$cachetime = ($this->config->get('pk_ts3_cache')) ? $this->config->get('pk_ts3_cache') : '30'; //default cachetime = 30 seconds
		if ($this->HMODE) {$cachetime = '90';} //fix to 90 seconds in hosting mode
		
		$this->tpl->css_file($this->root_path . 'portal/teamspeak3/TeamSpeakViewer/ts3view.css');
		
		$htmlout = $this->pdc->get('portal.modul.ts3.outputdata.'.$this->root_path, false, true);
		if ((!$htmlout) or $cachetime == '0'){
			include_once($this->root_path . 'portal/teamspeak3/TeamSpeakViewer/Ts3Viewer.php');
			$ts3v = registry::register('Ts3Viewer');

			if ($ts3v->connect()) {
				$ts3v->query();
				$ts3v->disconnect();
			}

			$htmlout = $ts3v->gethtml();
			unset($ts3v);
			if ($cachetime >= '1') {$this->pdc->put('portal.modul.ts3.outputdata.'.$this->root_path, $htmlout, $cachetime, false, true);}
		}

		$out  = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="colorswitch">';
		$out .= '<tr><td>';
		$out .= $htmlout;
		$out .= '</td></tr>';
		$out .= '</table>';
		return $out;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_teamspeak3_portal', teamspeak3_portal::__shortcuts());
?>