<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2012-11-11 19:07:23 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12435 $
 *
 * $Id: latestposts.php 12435 2012-11-11 18:07:23Z wallenium $
 */

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_latestposts')){
	class exchange_latestposts extends gen_class{
		public static $shortcuts = array('pex'=>'plus_exchange', 'user', 'core', 'time', 'db', 'pdc', 'config', 'bridge', 'crypt'=>'encrypt', 'pdh');
		public $options		= array();

		public function get_latestposts($params, $body){
			$intNumber = (intval($params['get']['number']) > 0) ?  intval($params['get']['number']) : 10;
		
			$myOut = $this->pdc->get('portal.modul.latestposts.exchange.'.$intNumber.'.u'.$this->user->id,false,true);
				if(!$myOut){
					// Select the Database to use.. (same, bridged mode, other)
				if($this->config->get('cmsbridge_active') == 1 && $this->config->get('pk_latestposts_dbmode') == 'bridge'){
					$mydb		= $this->bridge->db;
					//change prefix
					if (strlen(trim($this->config->get('pk_latestposts_dbprefix')))) $mydb->set_prefix(trim($this->config->get('pk_latestposts_dbprefix')));
				}elseif($this->config->get('pk_latestposts_dbmode') == 'new'){
					$mydb = dbal::factory(array('dbtype' => 'mysql', 'die_gracefully' => true, 'debug_prefix' => 'latestposts_', 'table_prefix' => trim($this->config->get('pk_latestposts_dbprefix'))));
					$mydb->open($this->crypt->decrypt($this->config->get('pk_latestposts_dbhost')), $this->crypt->decrypt($this->config->get('pk_latestposts_dbname')), $this->crypt->decrypt($this->config->get('pk_latestposts_dbuser')), $this->crypt->decrypt($this->config->get('pk_latestposts_dbpassword')));
				}else{
					$mydb = dbal::factory(array('dbtype' => 'mysql', 'die_gracefully' => true, 'debug_prefix' => 'latestposts_', 'table_prefix' => trim($this->config->get('pk_latestposts_dbprefix'))));
					$mydb->open($this->dbhost, $this->dbname, $this->dbuser, $this->dbpass);
				}
				
				$black_or_white	= ($this->config->get('pk_latestposts_blackwhitelist') == 'white') ? 'IN' : 'NOT IN';
				
				// include the BB Module File...
				$bbModule = $this->root_path . 'portal/latestposts/bb_modules/'.$this->config->get('pk_latestposts_bbmodule').'.php';
				if(is_file($bbModule)){
					include_once($bbModule);
					$classname = 'latestpostsmodule_'.$this->config->get('pk_latestposts_bbmodule');
					$module = new $classname();
					
					if(!$module || !method_exists($module, 'getBBQuery')){
						return $this->pex->error('boardmodule not available');
					}

				} else {
					return $this->pex->error('no boardmodule selected');
				}
				
				// Create Array of allowed/disallowed forums
				$arrUserMemberships = $this->pdh->get('user_groups_users', 'memberships', array($this->user->id));
				array_push($arrUserMemberships, 0);
				$arrForums = array();
				foreach ($arrUserMemberships as $groupid){
					$strForums = $this->config->get('pk_latestposts_privateforums_'.$groupid);
					if (method_exists($module, 'getBBForumQuery')){
						//serialized IDs
						$arrTmpForums = @unserialize($strForums);
						if (is_array($arrTmpForums)){
							foreach ($arrTmpForums as $forumid){
								$arrForums[] = $forumid;
							}
						}
					} else {
						//comma seperated IDs
						$arrTmpForums = ($this->config->get('pk_latestposts_privateforums')) ? explode(",", $this->config->get('pk_latestposts_privateforums')) : '';
						if(is_array($arrTmpForums)){
							foreach($arrTmpForums as $forumid){
								if(trim($forumid) != ''){
									$arrForums[] = trim($forumid);
								}
							}
						}
					}
				}		
				
				$strQuery = $module->getBBQuery($arrForums, $black_or_white, $intNumber);
				$myOut['forum_url'] = htmlentities($this->config->get('pk_latestposts_url'));
				
				$strBoardURL = $this->config->get('pk_latestposts_url');
				if (substr($this->config->get('pk_latestposts_url'), -1) != "/"){
					$strBoardURL .= '/';
				}
				
				$myOut['posts'] = array();
				$sucess = false;
				if($bb_result = $mydb->query($strQuery)){
					$sucess = true;
					while($row = $mydb->fetch_record($bb_result)){
						$myOut['posts'][] = array(
							'member_link' => htmlentities($strBoardURL.$module->getBBLink('member', $row)),
							'topic_link' => htmlentities($strBoardURL.$module->getBBLink('topic', $row)),
							'topic_title' => $row['bb_topic_title'],
							'topic_replies' => intval($row['bb_replies']),
							'topic_lastpost_date' => $this->time->date('Y-m-d H:i', $row['bb_posttime']),
							'topic_lastpost_timestamp' => $row['bb_posttime'],
							'topic_lastpost_username' => $row['bb_username'],				
						);
					}
				}else{
					$myOut['posts'] = array();
				}
				
				if ($sucess) {
					$this->pdc->put('portal.modul.latestposts.exchange.'.$intNumber.'.u'.$this->user->id,$myOut,300,false,true);
				}
				
			}
			return $myOut;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_latestposts', exchange_latestposts::$shortcuts);
?>