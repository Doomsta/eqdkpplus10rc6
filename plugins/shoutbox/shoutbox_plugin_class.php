<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-11-11 13:32:45 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 12426 $
 *
 * $Id: shoutbox_plugin_class.php 12426 2012-11-11 12:32:45Z godmod $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');
  exit;
}


/*+----------------------------------------------------------------------------
  | shoutbox
  +--------------------------------------------------------------------------*/
class shoutbox extends plugin_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('user', 'config', 'pdc', 'pfh', 'pdh', 'tpl');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  public $version    = '0.3.4';
  public $build      = '11404';
  public $copyright  = 'Aderyn';
  public $vstatus    = 'Beta';

  /**
    * Constructor
    * Initialize all informations for installing/uninstalling plugin
    */
  public function __construct()
  {
    parent::__construct();

    $this->add_data(array (
      'name'              => 'Shoutbox',
      'code'              => 'shoutbox',
      'path'              => 'shoutbox',
      'contact'           => 'Aderyn@gmx.net',
      'template_path'     => 'plugins/shoutbox/templates/',
      'icon'              => $this->root_path.'plugins/shoutbox/images/adminmenu/shoutbox.png',
      'version'           => $this->version,
      'author'            => $this->copyright,
      'description'       => $this->user->lang('sb_short_desc'),
      'long_description'  => $this->user->lang('sb_long_desc'),
      'homepage'          => EQDKP_PROJECT_URL,
      'manuallink'        => false,
      'plus_version'      => '1.0',
      'build'             => $this->build,
    ));

    $this->add_dependency(array(
      'plus_version'      => '0.7'
    ));

    // -- Register our permissions ------------------------
    // permissions: 'a'=admins, 'u'=user
    // ('a'/'u', Permission-Name, Enable? 'Y'/'N', Language string, array of user-group-ids that should have this permission)
    // Groups: 2 = Super-Admin, 3 = Admin, 4 = Member
    $this->add_permission('a', 'delete', 'N', $this->user->lang('delete'), array(2,3));
	$this->add_permission('u', 'view',    'Y', $this->user->lang('view'),    array(1,2,3,4));
    $this->add_permission('u', 'add',    'Y', $this->user->lang('add'),    array(2,3,4));

    // -- Menu --------------------------------------------
    $this->add_menu('admin_menu', $this->gen_admin_menu());

    // -- Portal Module -----------------------------------
    $this->add_portal_module('shoutbox');

    // -- PDH Modules -------------------------------------
    $this->add_pdh_read_module('shoutbox');
    $this->add_pdh_write_module('shoutbox');

    // -- Exchange Modules --------------------------------
    $this->add_exchange_module('shoutbox_add');
    $this->add_exchange_module('shoutbox_list');
    //$this->add_exchange_module('shoutbox', true, 'shoutbox.xml');

    // -- Hooks -------------------------------------------
    $this->add_hook('search', 'shoutbox_search_hook', 'search');
  }

  /**
    * pre_install
    * Define Installation
    */
  public function pre_install()
  {
    // include SQL and default configuration data for installation
    include($this->root_path.'plugins/shoutbox/includes/data/sql.php');
    include($this->root_path.'plugins/shoutbox/includes/data/config.php');

    // define installation
    for ($i = 1; $i <= count($shoutboxSQL['install']); $i++)
      $this->add_sql(SQL_INSTALL, $shoutboxSQL['install'][$i]);

    // insert configuration
    if (is_array($config_vars))
      $this->config->set($config_vars, '', 'shoutbox');
  }

  /**
    * pre_uninstall
    * Define uninstallation
    */
  public function pre_uninstall()
  {
    // include SQL data for uninstallation
    include($this->root_path.'plugins/shoutbox/includes/data/sql.php');

    for ($i = 1; $i <= count($shoutboxSQL['uninstall']); $i++)
      $this->add_sql(SQL_UNINSTALL, $shoutboxSQL['uninstall'][$i]);
  }

  /**
    * post_uninstall
    * Define Post Uninstall
    */
  public function post_uninstall()
  {
    // clear cache
    $this->pdc->del('pdh_shoutbox_table');

    // clear RSS feed
    $this->pfh->Delete($this->pfh->FilePath('shoutbox.xml', 'shoutbox'));
  }

  /**
    * gen_admin_menu
    * Generate the Admin Menu
    */
  private function gen_admin_menu()
  {
    $admin_menu = array (array(
        'name' => $this->user->lang('shoutbox'),
        'icon' => './../../plugins/shoutbox/images/adminmenu/shoutbox.png',
        1 => array (
          'link'  => 'plugins/shoutbox/admin/settings.php'.$this->SID,
          'text'  => $this->user->lang('settings'),
          'check' => 'a_shoutbox_',
          'icon'  => 'manage_settings.png'
        ),
        2 => array (
          'link'  => 'plugins/shoutbox/admin/manage.php'.$this->SID,
          'text'  => $this->user->lang('sb_manage_archive'),
          'check' => 'a_shoutbox_delete',
          'icon'  => './../glyphs/archive.png'
        )

    ));

    return $admin_menu;
  }

}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_shoutbox', shoutbox::__shortcuts());

?>
