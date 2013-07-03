<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2012-01-05 09:19:22 +0100 (Thu, 05 Jan 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: Godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11597 $
 * 
 * $Id: cronjob.php 11597 2012-01-05 08:19:22Z Godmod $
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
$lite = true;
include_once($eqdkp_root_path . 'common.php');

@set_time_limit(0);
@ignore_user_abort(true);
//single task
if(registry::register('input')->get('task') != ""){
	$force_run = (registry::register('input')->get('force') == 'true' && register('user')->check_auth('a_config_man', false)) ? true : false;
	registry::register('timekeeper')->execute_cron(registry::register('input')->get('task'), $force_run);
}else{
	//all cronjobs with flag external
	registry::register('timekeeper')->handle_crons(true);
}
?>