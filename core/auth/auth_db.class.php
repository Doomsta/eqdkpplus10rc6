<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2013-03-15 15:04:23 +0100 (Fri, 15 Mar 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13209 $
 * 
 * $Id: auth_db.class.php 13209 2013-03-15 14:04:23Z godmod $
 */
 
if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('auth')) include_once(registry::get_const('root_path').'core/auth.class.php');

class auth_db extends auth {
	public static function __shortcuts() {
		$shortcuts = array();
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	
	public static function __dependencies() {
		$dependencies = array();
		return array_merge(parent::__dependencies(), $dependencies);
	}
	
	public $error = false;
	
	public function pdl_html_format_login($log_entry) {
		return $log_entry['args'][0];
	}
	
	/**
	* Attempt to log in a user
	*
	* @param $strUsername
	* @param $strPassword
	* @param $boolSetAutoLogin Save login in cookie?
	* @param $boolUseHash Use Hash for comparing
	* @return bool
	*/
	public function login($strUsername, $strPassword, $boolSetAutoLogin = false, $boolUseHash = false){
		if(!$this->pdl->type_known("login")) $this->pdl->register_type("login", false, array($this, 'pdl_html_format_login'), array(3,4));
		
		$arrStatus = false;
		$this->error = false;
		
		//Bridge-Login, only if using not a hash
		if ($this->config->get('cmsbridge_active') == 1 && $this->config->get('pk_maintenance_mode') != 1 && $boolUseHash == false){
			$this->pdl->log('login', 'Try Bridge Login');
			$arrStatus = $this->bridge->login($strUsername, $strPassword, $boolSetAutoLogin, false);
		}
		
		//Bridge Login failed, Auth-Method Login
		if (!$arrStatus){
			$this->pdl->log('login', 'Bridge Login failed or Bridge not activated');
			//Login-Method Login like OpenID, Facebook, ...
			if ($this->in->get('lmethod') != ""){
				$this->pdl->log('login', 'Try Auth-Method Login '.$this->in->get('lmethod'));
				$arrAuthObject = $this->get_login_objects($this->in->get('lmethod'));
				if ($arrAuthObject) $arrStatus = $arrAuthObject->login($strUsername, $strPassword, $boolUseHash);
				if ($arrStatus) $this->pdl->log('login', 'Auth-Method Login '.$this->in->get('lmethod').' successful');
			}
			
			//Auth Login, because all other failed
			if (!$arrStatus){
				$this->pdl->log('login', 'Try EQdkp Plus Login');
				$result	= $this->db->query("SELECT user_id, username, user_password, user_email, user_active, api_key, failed_login_attempts, user_login_key
								FROM __users 
								WHERE LOWER(username) = '".$this->db->escape(clean_username($strUsername))."'");
				$row	= $this->db->fetch_record($result);
				
				if($row){		
					$this->db->free_result($result);
					list($strUserPassword, $strUserSalt) = explode(':', $row['user_password']);
					//If it's an old password without salt or there is a better algorythm
					$blnNeedsUpdate = $this->checkIfHashNeedsUpdate($strUserPassword) || !$strUserSalt;
					if($blnNeedsUpdate || $row['api_key'] == ''){
					if (((int)$row['user_active'])){
						$this->pdl->log('login', 'EQDKP User needs update');
						if($this->checkPassword($strPassword, $row['user_password'], $boolUseHash)){
							
								$strNewSalt		= $this->generate_salt();
								$strNewPassword	= $this->encrypt_password($strPassword, $strNewSalt);
								$strApiKey		= $this->generate_apikey($strPassword, $strNewSalt);
								
								$this->db->query("UPDATE __users 
														SET user_password='".$this->db->escape($strNewPassword.':'.$strNewSalt)."',
														api_key='".$this->db->escape($strApiKey)."'
														WHERE user_id='".$this->db->escape($row['user_id'])."'");
																		
								$arrStatus = array(
									'status'	=> 1,
									'user_id'	=> (int)$row['user_id'],
									'password_hash'	=> $strNewPassword,
									'user_login_key' => $row['user_login_key'],
								);
							} else {
								$this->pdl->log('login', 'EQDKP Login failed: wrong password');
								$this->error = 'wrong_password';
							}
						} else {
							$this->error = 'user_inactive';
							if ($row['failed_login_attempts'] >= (int)$this->config->get('failed_logins_inactivity') ){
								$this->error = 'user_inactive_failed_logins';
							}
							$this->pdl->log('login', 'EQDKP Login failed: '.$this->error);
						}
						
					}else{
						$strLoginPassword = $this->checkPassword($strPassword, $row['user_password'], $boolUseHash, true);
						if ((int)$row['user_active']){
							if($strLoginPassword){
								$arrStatus = array(
									'status'	=> 1,
									'user_id'	=> (int)$row['user_id'],
									'password_hash'	=> $strLoginPassword,
									'user_login_key' => $row['user_login_key'],
								);
							} else {
								$this->error = 'wrong_password';
								$this->pdl->log('login', 'EQDKP Login failed: '.$this->error);
							}	
						} else {
							$this->error = 'user_inactive';
							if ($row['failed_login_attempts'] >= (int)$this->config->get('failed_logins_inactivity') ){
								$this->error = 'user_inactive_failed_logins';
							}
							$this->pdl->log('login', 'EQDKP Login failed: '.$this->error);
						}
						
					}
				} else {
					$this->error = 'wrong_username';
					$this->pdl->log('login', 'EQDKP Login failed: '.$this->error);
				}
			}

			//If Bridge is active, check if EQdkp User is allowed to login
			if ($arrStatus && $this->config->get('cmsbridge_active') == 1 && (int)$this->config->get('pk_maintenance_mode') != 1){
				
				$this->pdl->log('login', 'Check EQdkp Plus User against Bridge Groups');
				//Only CMS User are allowed to login
				if ((int)$this->config->get('cmsbridge_onlycmsuserlogin')){
					$this->pdl->log('login', 'Only CMS User are allowed to login');
					//check if user is Superadmin, if yes, login
					$blnIsSuperadmin = $this->check_group(2, false, (int)$arrStatus['user_id']);
					
					//try Bridge-Login without passwort
					if (!$blnIsSuperadmin){
						$this->pdl->log('login', 'User ist not Superadmin, check against Bridge Groups');
						$arrStatus = $this->bridge->login($this->pdh->get('user', 'name', array((int)$arrStatus['user_id'])), false, false, $boolUseHash, false, false);
					}

					//deny access if not Superadmin and not in the groups
					if (!$blnIsSuperadmin && !$arrStatus){
						$arrStatus = false;
					}
				} else {
					//Everyone is allowed to login
					$this->pdl->log('login', 'Checks complete, call Bridge SSO if needed');
					//Bridge-Login without password, for settings Single Sign On
					$this->bridge->login($this->pdh->get('user', 'name', array((int)$arrStatus['user_id'])), false, false, $boolUseHash, false, false);
				}
			

			}
		}
		
		if (!$arrStatus){
			$this->pdl->log('login', 'User login failed');
			
			$this->db->query("UPDATE __sessions SET session_failed_logins = session_failed_logins + 1 WHERE session_id=?", false, $this->sid);
			$this->data['session_failed_logins']++;
			
			//Failed Login
			if ($this->config->get('pk_maintenance_mode') != 1){ //Only do this if not in MMode
				$userid = $this->pdh->get('user', 'userid', array($strUsername));
				if ($userid != ANONYMOUS && $this->pdh->get('user', 'active', array($userid))){
					$intFailedLogins = $this->pdh->get('user', 'failed_logins', array($userid));
					$intFailedLogins++;
					$this->pdh->put('user', 'update_failed_logins', array($userid, $intFailedLogins));

					//Set him inactive
					if ((int)$this->config->get('failed_logins_inactivity') > 0 && $intFailedLogins == (int)$this->config->get('failed_logins_inactivity')){
						$this->pdh->put('user', 'activate', array($userid, 0));
						
						//Write to admin-Log
						$this->logs->add('action_user_failed_logins', '', false, '', 1, $userid);
						
						//Send the User an Email with activation link
						$user_key = $this->pdh->put('user', 'create_new_activationkey', array($userid));
						
						// Email them their new key
						$email = registry::register('MyMailer');
						$bodyvars = array(
							'USERNAME'		=> $strUsername,
							'U_ACTIVATE'	=> $this->env->link.'register.php?mode=activate&key=' . $user_key,
						);
						$email->SendMailFromAdmin($this->pdh->get('user', 'email', array($userid)), $this->lang('email_subject_activation_self'), 'user_activation_failed_logins.html', $bodyvars);
					}
					
				}
			}
			
		} else {
			$this->pdl->log('login', 'User successfull authenticated');
			//User successfull authenticated - destroy old session and create a new one
			$this->db->query("UPDATE __users SET :params WHERE user_id=?", array('failed_login_attempts' => 0), $arrStatus['user_id']);
			$this->destroy();
			$this->create($arrStatus['user_id'], (isset($arrStatus['user_login_key']) ? $arrStatus['user_login_key'] : ''), ((isset($arrStatus['autologin'])) ? $arrStatus['autologin'] : $boolSetAutoLogin));
			return true;	
		}
		return false;
	}
	
	
	/**
	* Autologin
	*
	* @param $arrCookieData The Data ot the Session-Cookies
	* @return bool
	*/
	public function autologin($arrCookieData){
		$intCookieUserID = (isset($arrCookieData['data']['user_id'])) ? intval($arrCookieData['data']['user_id']) : ANONYMOUS;
		$strCookieAutologinKey = (isset($arrCookieData['data']['auto_login_id'])) ? $arrCookieData['data']['auto_login_id'] : '';
		
		if (isset($intCookieUserID) && intval($intCookieUserID) > 0){
			
			$query = $this->db->query("SELECT *
								FROM __users
								WHERE user_id = ?", false, $intCookieUserID);
			$arrUserResult = $this->db->fetch_record($query);
			$this->db->free_result($query);
			
			if ($arrUserResult){
				if ($strCookieAutologinKey != "" && $strCookieAutologinKey===$arrUserResult['user_login_key'] && (int)$arrUserResult['user_active']){
					return $arrUserResult;
				}
			}	
		}
		
		return false;
	}
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_auth_db',auth_db::__shortcuts());
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('dep_auth_db',auth_db::__dependencies());
?>