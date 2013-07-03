<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2011-09-03 12:39:53 +0200 (Sa, 03. Sep 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: Godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11214 $
 * 
 * $Id: load.php 11214 2011-09-03 10:39:53Z Godmod $
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');

if(registry::register('input')->get('loadrss')){
	registry::register('jquery')->loadRssFeed(registry::register('core')->config('pk_rssfeed_url'));
}
?>