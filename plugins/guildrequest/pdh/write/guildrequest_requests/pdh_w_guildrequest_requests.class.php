<?php
/*
 * Project:     EQdkp guildrequest_requests
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-11-01 13:38:39 +0100 (Di, 01. Nov 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest_requests
 * @version     $Rev: 11419 $
 *
 * $Id: pdh_w_guildrequest_requests.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
  die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_w_guildrequest_requests
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_w_guildrequest_requests'))
{
  class pdh_w_guildrequest_requests extends pdh_w_generic
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('db', 'pdh', 'time');
      return array_merge(parent::$shortcuts, $shortcuts);
    }

	public function add($strName, $strEmail, $strAuthKey, $strActivationKey, $strContent, $intActivated=1){
		
		$resQuery = $this->db->query("INSERT INTO __guildrequest_requests :params", array(
            'tstamp'        => $this->time->time,
			'username'		=> $strName,
			'email'			=> register('encrypt')->encrypt($strEmail),
			'auth_key'		=> $strAuthKey,
			'lastvisit'		=> 0,
			'activation_key'=> $strActivationKey,
			'status'		=> 0,
			'activated'		=> $intActivated,
			'closed'		=> 0,
			'content'		=> $strContent,
			'voting_yes'	=> 0,
			'voting_no'		=> 0,
			'voted_user'	=> '',
		));
		$this->pdh->enqueue_hook('guildrequest_requests_update');
		if ($resQuery) return $this->db->insert_id();
		
		return false;
	}
	
	public function delete($intID){
		$this->db->query("DELETE FROM __guildrequest_requests WHERE id=?", false, $intID);
		$this->pdh->enqueue_hook('guildrequest_requests_update');
		return true;
	}
	
	public function set_lastvisit($intID){
		$resQuery = $this->db->query("UPDATE __guildrequest_requests SET :params WHERE id=?", array(
			'lastvisit'		=> $this->time->time,
		), $intID);
		$this->pdh->enqueue_hook('guildrequest_requests_update');
		if ($resQuery) return $intID;
		
		return false;
	}
	
	public function truncate(){
		$this->db->query("TRUNCATE __guildrequest_requests");
		$this->pdh->enqueue_hook('guildrequest_requests_update');
		return true;
	}
	
	public function update_voting($intID, $intYes, $intNo, $arrVotedUser){
		$resQuery = $this->db->query("UPDATE __guildrequest_requests SET :params WHERE id=?", array(
			'voting_yes'	=> $intYes,
			'voting_no'		=> $intNo,
			'voted_user'	=> serialize($arrVotedUser),
		), $intID);
		$this->pdh->enqueue_hook('guildrequest_requests_update');
		if ($resQuery) return $intID;
		
		return false;
	}
	
	public function close($intID){
		$resQuery = $this->db->query("UPDATE __guildrequest_requests SET :params WHERE id=?", array(
			'closed'	=> 1,
		), $intID);
		$this->pdh->enqueue_hook('guildrequest_requests_update');
		if ($resQuery) return $intID;
		
		return false;
	}
	
	public function open($intID){
		$resQuery = $this->db->query("UPDATE __guildrequest_requests SET :params WHERE id=?", array(
			'closed'	=> 0,
		), $intID);
		$this->pdh->enqueue_hook('guildrequest_requests_update');
		if ($resQuery) return $intID;
		
		return false;
	}
	
	public function update_status($intID, $intStatus){
		$resQuery = $this->db->query("UPDATE __guildrequest_requests SET :params WHERE id=?", array(
			'status'	=> $intStatus,
		), $intID);
		$this->pdh->enqueue_hook('guildrequest_requests_update');
		if ($resQuery) return $intID;
		
		return false;
	}
   
    

  } //end class
} //end if class not exists

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_guildrequest_requests', pdh_w_guildrequest_requests::__shortcuts());
?>
