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
 * $Id: update_shoutbox_033.class.php 11425 2011-11-05 16:50:34Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

if (!class_exists('update_shoutbox_033'))
{
  class update_shoutbox_033 extends sql_update_task
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
    public $version     = '0.3.3';    // new version
    public $name        = 'Shoutbox 0.3.3 Update';
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
          'update_shoutbox_033' => 'Shoutbox 0.3.3 Update Package',
          'update_function'     => 'Insert new max text length setting',
        ),
        'german' => array(
          'update_shoutbox_033' => 'Shoutbox 0.3.3 Update Paket',
          'update_function'     => 'Füge Einstellung für die Maximale Textlänge hinzu',
        ),
      );

      // init SQL querys
      $this->sqls = array(
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
      // set as 'core' config
      $this->config->set('sb_max_text_length', '160');
	  
	  return true;
    }

  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_update_shoutbox_033', update_shoutbox_033::__shortcuts());
?>
