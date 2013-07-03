<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2011-09-02 21:49:51 +0200 (Fr, 02. Sep 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11200 $
 * 
 * $Id: update.php 11200 2011-09-02 19:49:51Z wallenium $
 */

define('EQDKP_INC', true);

$eqdkp_root_path = './../../';
include_once($eqdkp_root_path.'common.php');
include_once($eqdkp_root_path.'portal/twitter/twittermodule.class.php');
$twitter = registry::register('twittermodule');
$twitter->updateRSS();
?>