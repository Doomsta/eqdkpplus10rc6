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
 * $Id: pdh_r_guildrequest_visits.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
  die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_r_guildrequest_visits
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_r_guildrequest_visits'))
{
  class pdh_r_guildrequest_visits extends pdh_r_generic
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('pdc', 'db', 'pdh', 'config', 'bbcode', 'time');
      return array_merge(parent::$shortcuts, $shortcuts);
    }

    /**
     * Data array loaded by initialize
     */
    private $data;

    /**
     * Hook array
     */
    public $hooks = array(
      'guildrequest_visits_update'
    );

    /**
     * reset
     * Reset guildrequest_visits read module by clearing cached data
     */
    public function reset()
    {
      $this->pdc->del('pdh_guildrequest_visits_table');
      unset($this->data);
    }

    /**
     * init
     * Initialize the guildrequest_visits read module by loading all information from db
     *
     * @returns boolean
     */
    public function init()
    {
      // try to get from cache first
      $this->data = $this->pdc->get('pdh_guildrequest_visits_table');
      if($this->data !== NULL)
      {
        return true;
      }

      // empty array as default
      $this->data = array();

      // read all guildrequest_visits entries from db
      $sql = 'SELECT
               *
              FROM `__guildrequest_visits`';
      $result = $this->db->query($sql);
      if ($result)
      {

        // add row by row to local copy
        while (($row = $this->db->fetch_record($result)))
        {
          $this->data[(int)$row['user_id']][(int)$row['request_id']] = array(
            'request_id' 	=> (int)$row['request_id'],
            'user_id'       => (int)$row['user_id'],
            'lastvisit'     => (int)$row['lastvisit'],
          );
        }
        $this->db->free_result($result);
      }

      // add data to cache
      $this->pdc->put('pdh_guildrequest_visits_table', $this->data, null);

      return true;
    }

	public function get_user_visists($intUserID){
		if (isset($this->data[$intUserID])){
			return $this->data[$intUserID];
		}
		return false;
	}

  } //end class
} //end if class not exists

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_guildrequest_visits', pdh_r_guildrequest_visits::__shortcuts());
?>
