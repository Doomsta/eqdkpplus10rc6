<?php
 /*
 * Project:     EQdkp-Plus Raidlogimport
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-11-11 16:50:58 +0100 (Sun, 11 Nov 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2008-2009 hoofy_leon
 * @link        http://eqdkp-plus.com
 * @package     raidlogimport
 * @version     $Rev: 12431 $
 *
 * $Id: functions.php 12431 2012-11-11 15:50:58Z wallenium $
 */

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 Not Found');
	exit;
}

if(!function_exists('stripslashes_array')) {
	function stripslashes_array($array) {
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}
}

function format_duration($seconds) {
    $periods = array(
        'hours' => 3600,
        'minutes' => 60,
        'seconds' => 1
    );

    $durations = array();
    $durations['hours'] = 0;
    $durations['minutes'] = 0;

    foreach ($periods as $period => $seconds_in_period) {
        if ($seconds >= $seconds_in_period) {
            $durations[$period] = floor($seconds / $seconds_in_period);
            $seconds -= $durations[$period] * $seconds_in_period;
        }
    }
    return $durations;

}

function fktMultiArraySearch($arrInArray,$varSearchValue) {
    foreach ($arrInArray as $key => $row){
        $ergebnis = array_search($varSearchValue, $row);
        if ($ergebnis) {
            $arrReturnValue[0] = $key;
            $arrReturnValue[1] = $ergebnis;
            return $arrReturnValue;
        }
    }
}

function deep_in_array($search, $array) {
	foreach($array as $value) {
		if(!is_array($value)) {
			if($search === $value) return true;
		} else {
			if(deep_in_array($search, $value)) return true;
		}
	}
	return false;
}

function lang2tpl() {
	return;
	register('tpl')->assign_vars(array(
        'L_DIFFICULTY' 	=> (register('config')->get('default_game') == 'wow') ? register('user')->lang('difficulty') : false,
		'S_DEACTIVATE_ADJ' => (register('rli')->config('deactivate_adj')) ? true : false
	));
}
?>