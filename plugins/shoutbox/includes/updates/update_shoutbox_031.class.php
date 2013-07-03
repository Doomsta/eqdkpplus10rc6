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
 * $Id: update_shoutbox_031.class.php 11425 2011-11-05 16:50:34Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

if (!class_exists('update_shoutbox_031'))
{
  class update_shoutbox_031 extends sql_update_task
  {
	/**
	 * __dependencies
	 * Get module dependencies
	 */
	public static function __shortcuts()
	{
		$shortcuts = array('config');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
  
    public $author      = 'Aderyn';
    public $version     = '0.3.1';    // new version
    public $name        = 'Shoutbox 0.3.1 Update';
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
          'update_shoutbox_031' => 'Shoutbox 0.3.1 Update Package',
		  'update_function'     => 'Set new user or character setting',
          // SQL
           1 => 'Insert new user or character setting',
           2 => 'Change to user or character field',
        ),
        'german' => array(
          'update_shoutbox_031' => 'Shoutbox 0.3.1 Update Paket',
		  'update_function'     => 'Setze neue Benutzer oder Charakter Einstellung',
          // SQL
           1 => 'Füge neuen Benutzer oder Charakter Einstellung hinzu',
           2 => 'Ändere in Benutzer oder Charakter Eintrag',
        ),
      );

      // init SQL querys
      $this->sqls = array(
         1 => 'INSERT INTO `__backup_cnf` (config_name, config_value, config_plugin) VALUES(\'sb_use_users\', \'0\', \'shoutbox\');',
         2 => 'ALTER TABLE `__shoutbox` CHANGE `member_id` `user_or_member_id` SMALLINT(5) NOT NULL DEFAULT \'-1\';',
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
	  // when updating, there will be members instead of users, so set new value in config
	  $this->config->set('sb_use_users', '0', 'shoutbox');
	
      return true;
    }

  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_update_shoutbox_031', update_shoutbox_031::__shortcuts());
?>
