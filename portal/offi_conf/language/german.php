<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-11-11 19:07:23 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12435 $
 * 
 * $Id: german.php 12435 2012-11-11 18:07:23Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'offi_conf'				=> 'Offisitzung - Themen',
	'offi_conf_name'		=> 'Offisitzungsthemen Modul',
	'offi_conf_desc'		=> 'Admins können Themen für die Offizierssitzung eintragen.',
	
	//settings
	'pk_oc_day'				=> 'Tag der Offisitzung',
	'pk_oc_end_time'		=> 'gewolltes Ende der Offisitzung (HH:MM)',
	'pk_oc_start_time'		=> 'gewollter Anfang der Offisitzung (HH:MM)',
	'pk_oc_time_type'		=> 'Start oder Ende der Offisitzung festlegen?',
	'pk_oc_date'			=> 'Datum der nächsten Offisitzung',
	'pk_oc_period2'			=> 'Offisitzung alle x Wochen',
	'pk_oc_period3'			=> 'Offisitzung am x. Wochentag des Monats',
	'pk_oc_type'			=> 'Art der Wiederholung',
	'oc_weekday'			=> 'an einem bestimmten Wochentag',
	'oc_xmonthday'			=> 'am x. Wochentag des Monats',
	'oc_manual'				=> 'manuelle Datumseingabe',
	'oc_monday'				=> 'Montag',
	'oc_tuesday'			=> 'Dienstag',
	'oc_wednesday'			=> 'Mittwoch',
	'oc_thursday'			=> 'Donnerstag',
	'oc_friday'				=> 'Freitag',
	'oc_saturday'			=> 'Samstag',
	'oc_sunday'				=> 'Sonntag',
	
	'oc_add_topic'			=> 'Thema hinzufügen',
	'oc_upd_topic'			=> 'Thema bearbeiten',
	'oc_next_topics'		=> 'Nächste Themen',
	'oc_topic_name'			=> 'Name',
	'oc_topic_time'			=> 'Besprechungsdauer (min)',
	'oc_topic_posi'			=> 'Sortierung',
	'oc_topic_desc'			=> 'Beschreibung',
	'oc_save'				=> 'Speichern',
	'oc_delete'				=> 'Löschen',
	'oc_save_success'		=> 'Speichern erfolgreich!',
	'oc_save_no_success'	=> 'Speichern nicht erfolgreich!',
	'oc_delete_success'		=> 'Löschen erfolgreich!',
	'oc_delete_no_success'	=> 'Löschen nicht erfolgreich!',
	'oc_no_name'			=> 'Kein Name',
	'oc_no_desc'			=> 'Ohne Beschreibung',
	'oc_creator'			=> 'erstellt von:',
	'oc_min'				=> 'min',
	'oc_next_oc'			=> 'Nächste Offisitzung',
	'oc_start'				=> 'Beginn',
	'oc_end'				=> 'Ende',
);
?>