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
 * $Id: english.php 12435 2012-11-11 18:07:23Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'offi_conf'				=> 'Officer Conference - Topics',
	'offi_conf_name'		=> 'Officer Conference Module',
	'offi_conf_desc'		=> 'Admins can post topics for officer conference.',

	//settings
	'pk_oc_day'				=> 'Day of Officer Conference',
	'pk_oc_end_time'		=> 'wanted end of officer conference (HH:MM)',
	'pk_oc_start_time'		=> 'wanted begin of officer conference (HH:MM)',
	'pk_oc_time_type'		=> 'Define start or end of officer conference?',
	'pk_oc_date'			=> 'Date of the next officer meeting',
	'pk_oc_period2'			=> 'Officer meeting every x weeks',
	'pk_oc_period3'			=> 'Officer meeting every x weekday of the month',
	'pk_oc_type'			=> 'Repeat-type',
	'oc_weekday'			=> 'on a certain weekday',
	'oc_xmonthday'			=> 'on the x. wekday of the month',
	'oc_manual'				=> 'manual date entry',
	'oc_monday'				=> 'Monday',
	'oc_tuesday'			=> 'Tuesday',
	'oc_wednesday'			=> 'Wednesday',
	'oc_thursday'			=> 'Thursday',
	'oc_friday'				=> 'Friday',
	'oc_saturday'			=> 'Saturday',
	'oc_sunday'				=> 'Sunday',

	'oc_add_topic'			=> 'Add Topic',
	'oc_upd_topic'			=> 'Edit Topic',
	'oc_next_topics'		=> 'Next Topics',
	'oc_topic_name'			=> 'Name',
	'oc_topic_time'			=> 'Time to talk (min)',
	'oc_topic_posi'			=> 'Sorting',
	'oc_topic_desc'			=> 'Description',
	'oc_save'				=> 'Save',
	'oc_delete'				=> 'Delete',
	'oc_save_success'		=> 'Saving successful!',
	'oc_save_no_success'	=> 'Saving not successful!',
	'oc_delete_success'		=> 'Deletion successful!',
	'oc_delete_no_success'	=> 'Deletion not successful!',
	'oc_no_name'			=> 'No Name',
	'oc_no_desc'			=> 'No Description',
	'oc_creator'			=> 'created by:',
	'oc_min'				=> 'min',
	'oc_next_oc'			=> 'Next Officer Conference',
	'oc_start'				=> 'Begin',
	'oc_end'				=> 'End',
);
?>