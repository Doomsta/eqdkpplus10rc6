<?php
/*
 * Project:     EQdkp guildrequest_fields
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-11-01 13:38:39 +0100 (Di, 01. Nov 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest_fields
 * @version     $Rev: 11419 $
 *
 * $Id: pdh_r_guildrequest_fields.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
  die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_r_guildrequest_fields
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_r_guildrequest_fields'))
{
  class pdh_r_guildrequest_fields extends pdh_r_generic
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
      'guildrequest_fields_update'
    );

    /**
     * reset
     * Reset guildrequest_fields read module by clearing cached data
     */
    public function reset()
    {
      $this->pdc->del('pdh_guildrequest_fields_table');
      unset($this->data);
    }

    /**
     * init
     * Initialize the guildrequest_fields read module by loading all information from db
     *
     * @returns boolean
     */
    public function init()
    {
      // try to get from cache first
      $this->data = $this->pdc->get('pdh_guildrequest_fields_table');
      if($this->data !== NULL)
      {
        return true;
      }

      // empty array as default
      $this->data = array();

      // read all guildrequest_fields entries from db
      $sql = 'SELECT
               *
              FROM `__guildrequest_fields`
              ORDER BY sortid ASC;';
      $result = $this->db->query($sql);
      if ($result)
      {

        // add row by row to local copy
        while (($row = $this->db->fetch_record($result)))
        {
          $this->data[(int)$row['id']] = array(
            'id' 			=> (int)$row['id'],
            'type'          => $row['type'],
            'name'          => $row['name'],
			'help'			=> $row['help'],
			'options'		=> $row['options'],
			'sortid'		=> (int)$row['sortid'],
			'required'		=> (int)$row['required'],
			'in_list'		=> (int)$row['in_list'],
          );
        }
        $this->db->free_result($result);
      }

      // add data to cache
      $this->pdc->put('pdh_guildrequest_fields_table', $this->data, null);

      return true;
    }

    /**
     * get_id_list
     * Return the list of guildrequest_fields ids
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
	
	public function get_type($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['type'];
		}
		return false;
	}
	
	public function get_name($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['name'];
		}
		return false;
	}
	
	public function get_help($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['help'];
		}
		return false;
	}
	
	public function get_options($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['options'];
		}
		return false;
	}
	
	public function get_sortid($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['sortid'];
		}
		return false;
	}
	
	public function get_required($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['required'];
		}
		return false;
	}
	
	public function get_in_list($intID){
		if (isset($this->data[$intID])){
			return $this->data[$intID]['in_list'];
		}
		return false;
	}

  } //end class
} //end if class not exists

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_guildrequest_fields', pdh_r_guildrequest_fields::__shortcuts());
?>
