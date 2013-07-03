<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-12-15 12:24:26 +0100 (Sa, 15. Dez 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12591 $
 * 
 * $Id: german.php 12591 2012-12-15 11:24:26Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'weather'					=> 'Wetter',
	'weather_desc'				=> 'Zeigt das aktuelle Wetter',
	'weather_name'				=> 'Wetter',
	'pk_weather_no_data'		=> 'Es wurde kein Land oder keine Stadt in deinem Userprofil gesetzt. Die Wetterdaten können so nicht abgerufen werden.',
	'pk_weather_tempformat'		=> 'Format der Temperatur (Standard: Celcius)',
	'pk_weather_geolocation'	=> 'Geolocation-Option des Browsers verwenden wenn keine Ortsdaten im Profil hinterlegt sind?',
	'pk_weather_fulllink'		=> 'Volle Vorhersage',
);
?>