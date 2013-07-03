<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-12-17 13:13:57 +0100 (Mo, 17. Dez 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12604 $
 * 
 * $Id: weather_portal.class.php 12604 2012-12-17 12:13:57Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class weather_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'pdh', 'pfh', 'core', 'db', 'tpl', 'config', 'puf'	=> 'urlfetcher', 'in');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'weather';
	protected $data		= array(
		'name'			=> 'Weather',
		'version'		=> '3.0.0',
		'author'		=> 'WalleniuM',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Shows the weather',
	);
	protected $positions = array('left1', 'left2', 'right');
	protected $settings	= array(
		'pk_weather_tempformat'		=> array(
			'name'		=> 'pk_weather_tempformat',
			'language'	=> 'pk_weather_tempformat',
			'property'	=> 'dropdown',
			'options'	=> array('C' => 'C','F' => 'F'),
		),
		'pk_weather_geolocation'		=> array(
			'name'		=> 'pk_weather_geolocation',
			'language'	=> 'pk_weather_geolocation',
			'property'	=> 'checkbox',
		),
	);
	protected $install	= array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'left2',
		'defaultnumber'		=> '11',
	);

	public function output() {
		$this->tpl->js_file($this->root_path.'portal/weather/js/jquery.simpleopenweather.js');
		
		$this->tpl->add_css("
			.weatherItem {
				padding: 0.8em;
				text-align: right;
				background-size:70px;
			}
			.weatherCity {
				text-transform: uppercase;
				font-size: 0.8em;
			}
			.weatherTemp {
				font-size: 2.0em;
				font-weight: bold;
			}
			.weatherDesc, .weatherCity  {
				font-weight: bold;
			}
			.weatherDesc {
				margin-bottom: 0.4em;
			}
		");
		
		$tempformat				= ($this->config->get('pk_weather_tempformat') == 'F') ? '°F' : '°C';
		$tempformat_js			= ($this->config->get('pk_weather_tempformat') == 'F') ? ',units: "fahrenheit"' : '';
		
		if($this->user->data['user_id'] > 0 &&($this->user->data['country'] != '' && $this->user->data['town'] != '')){
			$this->tpl->add_js('$("#weather").simpleopenweather({template: \'<div style="display: block;" class="toggle_container"><div style="white-space:normal;">	<div class="weatherItem" style="background-image:url({{icon}}); background-repeat: no-repeat;"><div class="weatherCity">{{place}}</div><div class="weatherTemp">{{temperature}} '.$tempformat.'</div><div class="weatherDesc">{{sky}}</div></div>\', lang:"'.$this->user->lang('XML_LANG').'"'.$tempformat_js.'});', "docready");
			return '<div id="weather" class="simpleopenweather" data-simpleopenweather-city="'.$this->user->data['town'].', '.$this->user->data['country'].'"></div>';
		}elseif($this->config->get('pk_weather_geolocation') == '1'){
			$this->tpl->add_js('
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(locationSuccess, locationError);
				}else{
					showError("Your browser does not support Geolocation!");
				}
		
				function locationSuccess(position) {
					$("#weather").simpleopenweather({latitude: position.coords.latitude, longitude: position.coords.longitude,template: \'<div style="display: block;" class="toggle_container"><div style="white-space:normal;">	<div class="weatherItem" style="background-image:url({{icon}}); background-repeat: no-repeat;"><div class="weatherCity">{{place}}</div><div class="weatherTemp">{{temperature}} '.$tempformat.'</div><div class="weatherDesc">{{sky}}</div></div>\', lang:"'.$this->user->lang('XML_LANG').'"'.$tempformat_js.'});
				}

				function locationError(error){
					showError(error);
				}
		
				function showError(msg){
					$("#error").html(msg);
				}', "docready");
			return '<div id="weather" class="simpleopenweather"></div>';
		}else{
			return $this->user->lang('pk_weather_no_data');
		}
		
	}

}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_weather_portal', weather_portal::__shortcuts());

?>