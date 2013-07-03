<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-11-05 17:50:34 +0100 (Sa, 05. Nov 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11425 $
 *
 * $Id: update_shoutbox_030.class.php 11425 2011-11-05 16:50:34Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

if (!class_exists('update_shoutbox_030'))
{
  class update_shoutbox_030 extends sql_update_task
  {
    /**
	 * __dependencies
	 * Get module dependencies
	 */
	public static function __shortcuts()
	{
		$shortcuts = array('db', 'config');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

    public $author      = 'Aderyn';
    public $version     = '0.3.0';    // new version
    public $name        = 'Shoutbox 0.3.0 Update';
    public $type        = 'plugin_update';
    public $plugin_path = 'shoutbox'; // important!

    /**
     * Constructor
     */
    public function __construct()
    {
      parent::__construct();

      // init language
      $this->langs = array(
        'english' => array(
          'update_shoutbox_030' => 'Shoutbox 0.3.0 Update Package',
          'update_function' => 'Copy config',
          // SQL
           1 => 'Delete guest setting',
           2 => 'Delete location setting',
        ),
        'german' => array(
          'update_shoutbox_030' => 'Shoutbox 0.3.0 Update Paket',
          'update_function' => 'Kopiere Einstellungen',
          // SQL
           1 => 'Entferne Gast Einstellung',
           2 => 'Entferne Positions Einstellung',
        ),
      );

      // init SQL querys
      $this->sqls = array(
         1 => 'DELETE FROM `__config` WHERE `config_name`=\'sb_invisible_to_guests\';',
         2 => 'DELETE FROM `__config` WHERE `config_name`=\'sb_input_box_below\';',
      );
    }

    /**
     * update_function
     * Execute update function
     *
     * @returns  true/false
     */
    public function update_function()
    {
      // default settings
      $new_settings = array();

      // copy all settings from shoutbox config table to core config
      $sql = 'SELECT config_name, config_value FROM `__shoutbox_config`;';
      $config_result = $this->db->query($sql);
      if ($config_result)
      {
        while (($row = $this->db->fetch_record($config_result)))
        {
          $new_settings[$row['config_name']] = $row['config_value'];
        }
        $this->db->free_result($config_result);
      }

      // insert settings into core config table
      $this->config->set($new_settings, '', 'shoutbox');

      // delete old config table
      $sql = 'DROP TABLE `__shoutbox_config`;';
      $this->db->query($sql);

      return true;
    }

  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_update_shoutbox_030', update_shoutbox_030::__shortcuts());
?>
