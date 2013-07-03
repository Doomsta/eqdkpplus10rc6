<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2012-08-15 17:40:44 +0200 (Wed, 15 Aug 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11931 $
 *
 * $Id: points.php 11931 2012-08-15 15:40:44Z wallenium $
 */

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_points')){
	class exchange_points extends gen_class{
		public static $shortcuts = array('user', 'pex'=>'plus_exchange');
		public $options		= array();

		public function get_points($params, $body){
			if ($this->user->check_auth('u_event_view', false) && $this->user->check_auth('u_member_view', false) && $this->user->check_auth('u_item_view', false)){
				include_once($eqdkp_root_path . 'core/data_export.class.php');
				$myexp = new content_export();
				$withMemberItems = (isset($params['get']['exclude_memberitems']) && $params['get']['exclude_memberitems'] == 'true') ? false : true;
				return $myexp->export($withMemberItems);
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_points', exchange_points::$shortcuts);
?>