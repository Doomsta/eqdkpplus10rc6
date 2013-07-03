<?php
/*
 * Project:     EQdkp guildrequest
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-11-11 18:36:16 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest
 * @version     $Rev: 12434 $
 *
 * $Id: guildrequest_portal_hook.class.php 12434 2012-11-11 17:36:16Z godmod $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | guildrequest_portal_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('guildrequest_portal_hook'))
{
  class guildrequest_portal_hook extends gen_class
  {
    /* List of dependencies */
    public static $shortcuts = array('user', 'pdh', 'tpl', 'core');

	/**
    * hook_portal
    * Do the hook 'portal'
    *
    * @return array
    */
	public function portal()
	{
		if ($this->user->check_auths(array('u_guildrequest_view', 'a_guildrequest_manage'), 'OR', false)){
			$arrRequests = $this->pdh->get('guildrequest_requests', 'id_list', array());
			$intNew = 0;
			$intOpen = 0;
			foreach($arrRequests as $id){
				if ($this->pdh->get('guildrequest_requests', 'is_new', array($id))){
					$intNew++;
				}
				if(!$this->pdh->get('guildrequest_requests', 'closed', array($id))){
					$intOpen++;
				}
			}
			
			$text = sprintf($this->user->lang('gr_notification'), $intNew);
			if($intOpen && $this->user->check_auth('a_guildrequest_manage', false)) $text .= ', '.sprintf($this->user->lang('gr_notification_open'), $intOpen);
			
			$this->tpl->assign_block_vars('personal_area_addition', array(
				'TEXT' => '<img src="'.$this->root_path.'plugins/guildrequest/images/adminmenu/guildrequest.png" alt="GuildRequest" /><a href="'.$this->root_path.'plugins/guildrequest/listrequests.php'.$this->SID.'"> '. $text.'</a>',
			));
			
			$arrGuildrequestSettings = $this->pdh->get('user', 'plugin_settings', array($this->user->id, 'guildrequest'));
			if (isset($arrGuildrequestSettings['gr_jgrowl_notifications']) && $arrGuildrequestSettings['gr_jgrowl_notifications'] && $intNew){
				$this->core->message('<a href="'.$this->root_path.'plugins/guildrequest/listrequests.php'.$this->SID.'">'.$text.'</a>', $this->user->lang('guildrequest'));
			}
		}
	}
  }
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	registry::add_const('short_guildrequest_portal_hook', guildrequest_portal_hook::$shortcuts);
}
?>