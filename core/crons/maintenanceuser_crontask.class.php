<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2013-01-04 20:22:48 +0100 (Fri, 04 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12722 $
 * 
 * $Id: maintenanceuser_crontask.class.php 12722 2013-01-04 19:22:48Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "maintenanceuser_crontask" ) ) {
	class maintenanceuser_crontask extends crontask {
		public static $shortcuts = array('db', 'pdh', 'config', 'crypt'=>'encrypt');

		public function __construct(){
			$this->defaults['description']	= 'Deleting Maintenance-user';
			$this->defaults['delay']		= false;
			$this->defaults['ajax']			= false;
		}

		public function run(){
			$muser = unserialize(stripslashes($this->crypt->decrypt($this->config->get('maintenance_user'))));
			if ($muser['user_id']){
				$this->db->query("DELETE FROM __users WHERE user_id = '".$this->db->escape($muser['user_id'])."'");

				$this->pdh->put('user_groups_users', 'delete_user_from_group', array($muser['user_id'], 2));
				
				$special_users = unserialize(stripslashes($this->config->get('special_user')));
				unset($special_users[$muser['user_id']]);
				$this->config->set('special_user', serialize($special_users));
			}
			$this->config->set('maintenance_user', '');
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_maintenanceuser_crontask', maintenanceuser_crontask::$shortcuts);
?>