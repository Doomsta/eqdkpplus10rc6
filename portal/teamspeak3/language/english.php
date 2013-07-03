<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2012-12-16 10:41:03 +0100 (So, 16. Dez 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12596 $
 * 
 * $Id: english.php 12596 2012-12-16 09:41:03Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'teamspeak3'					=> 'Teamspeak 3',
	'teamspeak3_name'				=> 'Teamspeak 3 Module',
	'teamspeak3_desc'				=> 'Teamspeak3 Server Information',
	'lang_pk_ts3_ip'				=> 'Your Server IP (without Port)',
	'lang_pk_ts3_port'				=> 'The port - Default: 9987',
	'lang_pk_ts3_telnetport'		=> 'The Telnet Port of your Server - Default: 10011',
	'lang_pk_ts3_id'				=> 'The ID from your Virtual Server - Default: 1',
	'lang_help_pk_ts3_id'			=> 'Enter -1 to use the servers port to connect instead of the servers id.',
	'lang_pk_ts3_cache'				=> 'Min. time between TS3-querys (seconds)',
	'lang_help_pk_ts3_cache'		=> 'How long should the TS3-data be cached in seconds before TS3 ist queried again. 0 for disable caching.',
	'lang_pk_ts3_banner'			=> 'Shows banner if URL is avaible in TS?',
	'lang_pk_ts3_join'				=> 'Show link to join the server?',
	'lang_pk_ts3_jointext'			=> 'Link text of the link to join the server',
	'lang_pk_ts3_legend'			=> 'Show groupinfo at the bottom?',
	'lang_pk_ts3_cut_names'			=> 'Cut Usernames',
	'lang_help_pk_ts3_cut_names'	=> 'If you want to abridge the usernames, set this to the desired size - No cut = 0',
	'lang_pk_ts3_cut_channel'		=> 'Cut Channelnames',
	'lang_help_pk_ts3_cut_channel'	=> 'If you want to abridge the channelnames, set this to the desired size - No cut = 0',
	'lang_pk_only_populated_channel'=> 'Show only populated channels?',
	'lang_pk_ts3_useron'			=> 'Show Online User / Possible Users?',
	'lang_pk_ts3_stats'				=> 'Show a statistic box under the TS viewer?',
	'lang_pk_ts3_stats_showos'		=> 'Show on wich OS TS3 runs?',
	'lang_pk_ts3_stats_version'		=> 'Show the TS3 server version?',
	'lang_pk_ts3_stats_numchan'		=> 'Show the number of channels?',
	'lang_pk_ts3_stats_uptime'		=> 'Show the server uptime since the last restart?',
	'lang_pk_ts3_stats_install'		=> 'Show when the server was installed?',
	'lang_pk_ts3_timeout'			=> "Timeout (DON'T change)",
	'lang_help_pk_ts3_timeout'		=> 'Leave this field blank, unless you are very sure what you are doing!',
	'lang_pk_ts3_hide_spacer'		=> 'Hide Channel Spacer',
);
?>