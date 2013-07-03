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
 * $Id: pdh_w_guildrequest_fields.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
  die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_w_guildrequest_fields
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_w_guildrequest_fields'))
{
  class pdh_w_guildrequest_fields extends pdh_w_generic
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

	public function add($intID, $strType, $strName, $strHelp, $arrOptions, $intSortID, $intRequired, $intInList = 0){
		$resQuery = $this->db->query("INSERT INTO __guildrequest_fields :params", array(
			'id'		=> $intID,
			'type'		=> $strType,
			'name'		=> $strName,
			'help'		=> $strHelp,
			'options'	=> serialize($arrOptions),
			'sortid' 	=> $intSortID,
			'required' 	=> $intRequired,
			'in_list'	=> $intInList,
		));
		$this->pdh->enqueue_hook('guildrequest_fields_update');
		if ($resQuery) return $this->db->insert_id();
		
		return false;
	}
	
	public function update($intID, $strType, $strName, $strHelp, $arrOptions, $intSortID, $intRequired, $intInList = 0){
		$resQuery = $this->db->query("UPDATE __guildrequest_fields SET :params WHERE id=?", array(
			'type'		=> $strType,
			'name'		=> $strName,
			'help'		=> $strHelp,
			'options'	=> serialize($arrOptions),
			'sortid' 	=> $intSortID,
			'required' 	=> $intRequired,
			'in_list'	=> $intInList,
		), $intID);
		$this->pdh->enqueue_hook('guildrequest_fields_update');
		if ($resQuery) return $intID;
		
		return false;
	}
	
	public function delete($intID){
		$this->db->query("DELETE FROM __guildrequest_fields WHERE id=?", false, $intID);
		$this->pdh->enqueue_hook('guildrequest_fields_update');
		return true;
	}
	
	public function truncate(){
		$this->db->query("TRUNCATE __guildrequest_fields");
		$this->pdh->enqueue_hook('guildrequest_fields_update');
		return true;
	}
   
    

  } //end class
} //end if class not exists

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_guildrequest_fields', pdh_w_guildrequest_fields::__shortcuts());
?>
