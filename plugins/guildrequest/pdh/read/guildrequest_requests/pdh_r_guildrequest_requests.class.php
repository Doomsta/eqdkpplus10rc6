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
 * $Id: pdh_r_guildrequest_requests.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
  die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_r_guildrequest_requests
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_r_guildrequest_requests'))
{
  class pdh_r_guildrequest_requests extends pdh_r_generic
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('pdc', 'db', 'pdh', 'config', 'time', 'user');
      return array_merge(parent::$shortcuts, $shortcuts);
    }
	
	public $presets = array(
		'gr_checkbox'	=> array('checkbox', array('%request_id%'), array()),
		'gr_date'		=> array('tstamp', array('%request_id%'), array()),
		'gr_name'		=> array('username', array('%request_id%'), array()),
		'gr_email'		=> array('email', array('%request_id%'), array()),
		'gr_status'		=> array('status', array('%request_id%'), array()),
		'gr_voting_flag'=> array('voting_flag', array('%request_id%'), array()),
		'gr_closed'=> array('closed', array('%request_id%'), array()),
	);

    /**
     * Data array loaded by initialize
     */
    private $data;

    /**
     * Hook array
     */
    public $hooks = array(
      'guildrequest_requests_update',
	  'guildrequest_visits_update'
    );

    /**
     * reset
     * Reset guildrequest_requests read module by clearing cached data
     */
    public function reset()
    {
      $this->pdc->del('pdh_guildrequest_requests_table');
      unset($this->data);
    }

    /**
     * init
     * Initialize the guildrequest_requests read module by loading all information from db
     *
     * @returns boolean
     */
    public function init()
    {
		$arrFields = $this->pdh->get('guildrequest_fields', 'id_list', array());
		foreach ($arrFields as $id){
			if ($this->pdh->get('guildrequest_fields', 'in_list', array($id)) && $this->pdh->get('guildrequest_fields', 'type', array($id)) < 3){
				$this->presets['gr_field_'.$id] = array('field', array('%request_id%', $id), array($id));
			}
		}	
	
      // try to get from cache first
      $this->data = $this->pdc->get('pdh_guildrequest_requests_table');
      if($this->data !== NULL)
      {
        return true;
      }

      // empty array as default
      $this->data = array();

      // read all guildrequest_requests entries from db
      $sql = 'SELECT
               *
              FROM `__guildrequest_requests`
              ORDER BY tstamp DESC;';
      $result = $this->db->query($sql);
      if ($result)
      {

        // add row by row to local copy
        while (($row = $this->db->fetch_record($result)))
        {
          $this->data[(int)$row['id']] = array(
            'id' 			=> (int)$row['id'],
            'tstamp'        => (int)$row['tstamp'],
			'username'		=> $row['username'],
			'email'			=> $row['email'],
			'auth_key'		=> $row['auth_key'],
			'lastvisit'		=> (int)$row['lastvisit'],
			'activation_key'=> $row['activation_key'],
			'status'		=> (int)$row['status'],
			'activated'		=> (int)$row['activated'],
			'closed'		=> (int)$row['closed'],
			'content'		=> $row['content'],
			'voting_yes'	=> (int)$row['voting_yes'],
			'voting_no'		=> (int)$row['voting_no'],
			'voted_user'	=> $row['voted_user'],
          );
        }
        $this->db->free_result($result);
      }

      // add data to cache
      $this->pdc->put('pdh_guildrequest_requests_table', $this->data, null);

      return true;
    }

    /**
     * get_id_list
     * Return the list of guildrequest_requests ids
     *
     * @returns array(int)
     */
    public function get_id_list()
    {
      if (is_array($this->data))
      {
        return array_keys($this->data);
      }
      return array();
    }

	public function get_id($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID];
		}
		return false;
	}
	
	public function get_tstamp($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['tstamp'];
		}
		return false;
	}
	
	public function get_html_tstamp($intID){
		if (isset($this->data[$intID])){
			return $this->time->user_date($this->data[$intID]['tstamp'], true);
		}
		return false;
	}
	
	public function get_username($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['username'];
		}
		return false;
	}
	
	public function get_html_username($intID){
		$strUsername = $this->get_username($intID);
		if ($this->get_is_new($intID)){
			return '<a href="viewrequest.php'.$this->SID.'&amp;id='.$intID.'"><b>'.$strUsername.'</b></a>';
		}
		return '<a href="viewrequest.php'.$this->SID.'&amp;id='.$intID.'">'.$strUsername.'</a>';
	}
	
	public function get_email($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['email'];
		}
		return false;
	}
	
	public function get_html_email($intID){
		if (isset($this->data[$intID])){
			return register('encrypt')->decrypt($this->data[$intID]['email']);
		}
		return false;
	}
	
	public function get_auth_key($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['auth_key'];
		}
		return false;
	}
	
	public function get_lastvisit($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['lastvisit'];
		}
		return false;
	}
	
	public function get_activation_key($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['activation_key'];
		}
		return false;
	}
	
	public function get_status($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['status'];
		}
		return false;
	}
	
	public function get_html_status($intID){
		if (isset($this->data[$intID])){
			$arrStatus = $this->user->lang('gr_status');
			return $arrStatus[$this->data[$intID]['status']];
		}
		return false;
	}
	
	public function get_activated($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['activated'];
		}
		return false;
	}
	public function get_closed($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['closed'];
		}
		return false;
	}
	
	public function get_html_closed($intID){
		if (isset($this->data[$intID])){
			if ($this->data[$intID]['closed']) return '<img src="'.$this->root_path.'images/calendar/closed_s.png" alt="closed"/>';
		}
		return '';
	}
	public function get_content($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['content'];
		}
		return false;
	}
	
	public function get_voting_yes($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['voting_yes'];
		}
		return false;
	}
	
	public function get_voting_no($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['voting_no'];
		}
		return false;
	}
	
	public function get_voted_user($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['voted_user'];
		}
		return false;
	}
	
	public function get_checkbox($intID){
		if ($this->get_is_new($intID)){
			return 'a_'.$this->get_tstamp($intID);
		} elseif($this->get_closed($intID)){
			return 'c_'.$this->get_tstamp($intID);
		} else {
			return 'b_'.$this->get_tstamp($intID);
		}
	}
	
	public function get_html_checkbox($intID){
		return '<input type="checkbox" name="gr[]" value="'.$intID.'" />';
	}
	
	public function get_is_new($intID){
		//Get user last visit
		$arrVisits = $this->pdh->get('guildrequest_visits', 'user_visists', array($this->user->id));
		if ($arrVisits && isset($arrVisits[$intID])){
			$intLastVisit = $arrVisits[$intID]['lastvisit'];
			//Get last comment
			$arrComments = $this->pdh->get('comment', 'filtered_list', array('guildrequest', $intID));
			foreach($arrComments as $key => $val){
				if ($val['date'] > $intLastVisit) return 1;
			}
			
			//Get last internal comment
			$arrComments = $this->pdh->get('comment', 'filtered_list', array('guildrequest_int', $intID));
			foreach($arrComments as $key => $val){
				if ($val['date'] > $intLastVisit) return 1;
			}
		} else {
			//New
			return 1;
		}
		return 0;
	}
	
	public function get_voting_flag($intID){
		$arrVotedUser = ($this->get_voted_user($intID) && $this->get_voted_user($intID)  != '') ? unserialize($this->get_voted_user($intID) ) : array();
		if (isset($arrVotedUser[$this->user->id])) {
			return ($arrVotedUser[$this->user->id] == 'yes') ? 1 : 0;
		}
		return -1;
	}
	
	public function get_html_voting_flag($intID){
		$intFlag = $this->get_voting_flag($intID);
		switch($intFlag){
			case 1: return '<img src="'.$this->root_path.'images/calendar/status/status0.png" alt="Yes" />';
			case 0: return '<img src="'.$this->root_path.'images/calendar/status/status2.png" alt="Yes" />'; 
		}
		
		return '';
	}
	
	public function get_field($intID, $intFieldID){
		$arrContent = unserialize($this->get_content($intID));
		if (isset($arrContent[$intFieldID])) return $arrContent[$intFieldID];
		return '';
	}
	
	public function get_html_caption_field($params){
		return $this->pdh->get('guildrequest_fields', 'name', array($params));
	}
	
	/**
     * get_search
     * Searches the shoutbox module for the search value
     *
     * @param  string  $search  Value to search
     *
     * @returns array
     */
    public function get_search($search)
    {
      // empty search results
      $searchResults = array();

      // loop through the data array and fill search results
      if ($this->data && is_array($this->data))
      {
		$arrStatus = $this->user->lang('gr_status');
	  
        foreach ($this->data as $id => $data)
        {
          $member = $data['username'];
		  $email = register('encrypt')->decrypt($data['email']);
		  $content = $data['content'];
		  

          if (strpos($member, $search) !== false || strpos( $email, $search) !== false || strpos( $content, $search) !== false)
          {
            $searchResults[] = array(
              'id'   => $this->time->user_date($data['tstamp'], true),
              'name' => $data['username'].'; '.$this->user->lang('status').': '.$arrStatus[$data['status']],
              'link' => $this->root_path.'plugins/guildrequest/viewrequest.php'.$this->SID.'&amp;id='.$id,
            );
          }
        }
      }

      return $searchResults;
    }

  } //end class
} //end if class not exists

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_guildrequest_requests', pdh_r_guildrequest_requests::__shortcuts());
?>
