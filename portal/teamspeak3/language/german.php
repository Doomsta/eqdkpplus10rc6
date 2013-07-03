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
 * $Id: german.php 12596 2012-12-16 09:41:03Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'teamspeak3'					=> 'Teamspeak 3',
	'teamspeak3_name'				=> 'Teamspeak 3 Modul',
	'teamspeak3_desc'				=> 'Teamspeak3 Server Information',
	'lang_pk_ts3_ip'				=> 'Die Server IP (ohne Port)',
	'lang_pk_ts3_port'				=> 'Der Port - Standard: 9987',
	'lang_pk_ts3_telnetport'		=> 'Der Telnet Port deines Servers - Standard: 10011',
	'lang_pk_ts3_id'				=> 'Die ID deines Server - Standard: 1',
	'lang_help_pk_ts3_id'			=> 'Gib -1 ein um anstatt der ID den Port deines Server zum Verbinden zu nutzen.',
	'lang_pk_ts3_cache'				=> 'Mindestabstand zwischen TS3-Abfragen (Sekunden)',
	'lang_help_pk_ts3_cache'		=> 'Gibt in Sekunden an wie lange vom TS3 abgefragte Daten zwischengespeichert werden sollen bevor der TS3 neu abgefragt wird. 0 um das Zwischenspeichern abzuschalten.',
	'lang_pk_ts3_banner'			=> 'Zeige das Banner, welches du im TS eingestellt hast?',
	'lang_pk_ts3_join'				=> 'Zeige Link zum Beitreten des TS-Servers?',
	'lang_pk_ts3_jointext'			=> 'Text des Links zum Beitreten des TS-Servers',
	'lang_pk_ts3_legend'			=> 'Zeig unter der Tabelle eine Übersicht der Gruppen an?',
	'lang_pk_ts3_cut_names'			=> 'Benutzernamen kürzen',
	'lang_help_pk_ts3_cut_names'	=> 'Wenn du die Usernamen auf eine bestimmte Länge kürzen willst, gib hier die Anzahl der Zeichen ein - Kein Kürzen = 0',
	'lang_pk_ts3_cut_channel'		=> 'Channelnamen kürzen',
	'lang_help_pk_ts3_cut_channel'	=> 'Wenn du die Channelnamen auf eine bestimmte Länge kürzen willst, gib hier die Anzahl der Zeichen ein - Kein Kürzen = 0',
	'lang_pk_only_populated_channel'=> 'Zeige nur Channel an, in denen sich auch jemand befindet?',
	'lang_pk_ts3_useron'			=> 'Zeige die Anzahl der Online User und möglichen User an?',
	'lang_pk_ts3_stats'				=> 'Zeige eine Statistikbox unter dem TS3-Viewer?',
	'lang_pk_ts3_stats_showos'		=> 'Zeige das OS des Servers in der Statistikbox?',
	'lang_pk_ts3_stats_version'		=> 'Zeige die Version des Servers in der Statistikbox?',
	'lang_pk_ts3_stats_numchan'		=> 'Zeige die Channelanzahl des Servers in der Statistikbox?',
	'lang_pk_ts3_stats_uptime'		=> 'Zeige die Laufzeit des Servers in der Statistikbox?',
	'lang_pk_ts3_stats_install'		=> 'Zeige das Installationsdatum des Servers in der Statistikbox?',
	'lang_pk_ts3_timeout'			=> 'Timeout (NICHT ändern)',
	'lang_help_pk_ts3_timeout'		=> 'Dieses Feld dringend leer lassen, es sei denn du weißt ganz genau was du tust!',
	'lang_pk_ts3_hide_spacer'		=> 'Channel-Spacer nicht anzeigen',
);
?>