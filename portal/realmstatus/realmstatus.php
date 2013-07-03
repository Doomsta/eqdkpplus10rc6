<?php
 /*
 * Project:   EQdkp-Plus
 * License:   Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:   2008
 * Date:    $Date: 2012-12-02 15:29:12 +0100 (So, 02. Dez 2012) $
 * -----------------------------------------------------------------------
 * @author    $Author: godmod $
 * @copyright 2008-2011 Aderyn
 * @link    http://eqdkp-plus.com
 * @package   eqdkp-plus
 * @version   $Rev: 12532 $
 *
 * $Id: realmstatus.php 12532 2012-12-02 14:29:12Z godmod $
 */
 
define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path.'common.php');

// load the portal language
registry::register('portal')->load_lang('realmstatus');

// get game the status is requested for
$game_name = registry::register('input')->get('game', 'unknown');
$game_name = strtolower($game_name);

// try to get a game status file for the requested game
$status_file = $eqdkp_root_path.'portal/realmstatus/'.$game_name.'/status.class.php';
if (file_exists($status_file))
{
  include_once($status_file);

  $class_name = $game_name.'_realmstatus';
  $status = registry::register($class_name);
  if ($status)
	$realmstatus = $status->getJQueryOutput();
  else
	$realmstatus = '<div class="center">'.register('user')->lang('rs_game_not_supported').'</div>';
}
else
{
  $realmstatus = '<div class="center">'.register('user')->lang('rs_game_not_supported').'</div>';
}

echo $realmstatus;

?>
