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
 * $Id: english.php 12591 2012-12-15 11:24:26Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'weather'					=> 'Weather',
	'weather_desc'				=> 'Shows the weather',
	'weather_name'				=> 'Weather',
	'pk_weather_no_data'		=> 'No Country or ZIP set in your user profile. Weather will only show information if you\'ve set up your Profile information.',
	'pk_weather_tempformat'		=> 'Temperature Format (default: Celcius)',
	'pk_weather_geolocation'	=> 'Use Geolocation-Option of the browser if no location data is setin the user profile?',
	'pk_weather_fulllink'		=> 'Full forecast',
);
?>