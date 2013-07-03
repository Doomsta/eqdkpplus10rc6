<?php
/*
 * Project:     EQdkp guildrequest_visits
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-11-01 13:38:39 +0100 (Di, 01. Nov 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest_visits
 * @version     $Rev: 11419 $
 *
 * $Id: pdh_w_guildrequest_visits.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
  die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_w_guildrequest_visits
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_w_guildrequest_visits'))
{
  class pdh_w_guildrequest_visits extends pdh_w_generic
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('db', 'pdh', 'time', 'user');
      return array_merge(parent::$shortcuts, $shortcuts);
    }
	
	public function add($intID){
		$resQuery = $this->db->query("REPLACE INTO __guildrequest_visits :params", array(
			'request_id'		=> $intID,
			'user_id'			=> $this->user->id,
			'lastvisit'			=> $this->time->time,
		), $intID);
		$this->pdh->enqueue_hook('guildrequest_visits_update');
		if ($resQuery) return $intID;
		
		return false;
	}
	
	public function truncate(){
		$this->db->query("TRUNCATE __guildrequest_visits");
		$this->pdh->enqueue_hook('guildrequest_visits_update');
		return true;
	}
   
    

  } //end class
} //end if class not exists

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_guildrequest_visits', pdh_w_guildrequest_visits::__shortcuts());
?>
