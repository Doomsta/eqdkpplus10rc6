<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
 * Date:		$Date: 2011-12-16 15:55:23 +0100 (Fri, 16 Dec 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11547 $
 * 
 * $Id: index.php 11547 2011-12-16 14:55:23Z hoofy $
 */

// IN_ADMIN not yet defined, display the main admin page
if(!defined('IN_ADMIN')){
	define('EQDKP_INC', true);
	define('IN_ADMIN', true);
	$eqdkp_root_path = './../';
	include_once($eqdkp_root_path . 'common.php');
	registry::register('admin_index')->display();
}
?>