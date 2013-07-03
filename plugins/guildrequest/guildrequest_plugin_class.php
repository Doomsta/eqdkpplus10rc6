<?php
/*
 * Project:     EQdkp guildrequest
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-11-11 13:32:45 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest
 * @version     $Rev: 12426 $
 *
 * $Id: guildrequest_plugin_class.php 12426 2012-11-11 12:32:45Z godmod $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');
  exit;
}


/*+----------------------------------------------------------------------------
  | guildrequest
  +--------------------------------------------------------------------------*/
class guildrequest extends plugin_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('user', 'config', 'pdc', 'pfh', 'pdh');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  public $version    = '0.1.10';
  public $build      = '';
  public $copyright  = 'GodMod';
  public $vstatus    = 'Beta';

  /**
    * Constructor
    * Initialize all informations for installing/uninstalling plugin
    */
  public function __construct()
  {
    parent::__construct();

    $this->add_data(array (
      'name'              => 'GuildRequest',
      'code'              => 'guildrequest',
      'path'              => 'guildrequest',
      'template_path'     => 'plugins/guildrequest/templates/',
      'icon'              => $this->root_path.'plugins/guildrequest/images/adminmenu/guildrequest.png',
      'version'           => $this->version,
      'author'            => $this->copyright,
      'description'       => $this->user->lang('guildrequest_short_desc'),
      'long_description'  => $this->user->lang('guildrequest_long_desc'),
      'homepage'          => EQDKP_PROJECT_URL,
      'manuallink'        => false,
      'plus_version'      => '1.0',
      'build'             => $this->build,
    ));

    $this->add_dependency(array(
      'plus_version'      => '1.0'
    ));

    // -- Register our permissions ------------------------
    // permissions: 'a'=admins, 'u'=user
    // ('a'/'u', Permission-Name, Enable? 'Y'/'N', Language string, array of user-group-ids that should have this permission)
    // Groups: 1 = Guests, 2 = Super-Admin, 3 = Admin, 4 = Member
	$this->add_permission('u', 'view',    'Y', $this->user->lang('gr_view'),    array(2,3,4));
	$this->add_permission('u', 'vote',    'Y', $this->user->lang('gr_vote'),    array(2,3,4));
	$this->add_permission('u', 'comment_int',    'Y', $this->user->lang('gr_internal_comment'),    array(2,3,4));
	$this->add_permission('u', 'comment',    'Y', $this->user->lang('gr_comment'),    array(1,2,3,4));
	$this->add_permission('u', 'add',    'Y', $this->user->lang('gr_add'),    array(1)); //Guests
    $this->add_permission('a', 'manage', 'N', $this->user->lang('manage'), array(2,3));
	$this->add_permission('a', 'form', 'N', $this->user->lang('gr_manage_form'), array(2,3));
	$this->add_permission('a', 'settings', 'N', $this->user->lang('menu_settings'), array(2,3));
	


    // -- PDH Modules -------------------------------------
    $this->add_pdh_read_module('guildrequest_fields');
	$this->add_pdh_read_module('guildrequest_requests');
	$this->add_pdh_read_module('guildrequest_visits');
    $this->add_pdh_write_module('guildrequest_fields');
	$this->add_pdh_write_module('guildrequest_requests');
	$this->add_pdh_write_module('guildrequest_visits');
    // -- Hooks -------------------------------------------
    $this->add_hook('search', 'guildrequest_search_hook', 'search');
	$this->add_hook('portal', 'guildrequest_portal_hook', 'portal');
	// -- Menu --------------------------------------------
    $this->add_menu('admin_menu', $this->gen_admin_menu());
	
	$this->add_menu('main_menu1', $this->gen_main_menu());
	$this->add_menu('settings', $this->usersettings());
	
  }

  /**
    * pre_install
    * Define Installation
    */
   public function pre_install()
  {
    // include SQL and default configuration data for installation
    include($this->root_path.'plugins/guildrequest/includes/sql.php');

    // define installation
    for ($i = 1; $i <= count($guildrequestSQL['install']); $i++)
      $this->add_sql(SQL_INSTALL, $guildrequestSQL['install'][$i]);
	  
	if($this->pdh->get('user', 'check_username', array('GuildRequest')) != 'false'){
		//Neu anlegen
		$arrUserdata = array(
			'email' => 'guildrequest@eqdkp.plugin',
			'name'	=> 'GuildRequest',
		);
		
		$salt = $this->user->generate_salt();
		$strPassword = random_string(false, 40);
		$strPwdHash = $this->user->encrypt_password($strPassword, $salt);
		$strApiKey = $this->user->generate_apikey($strPassword, $salt);
		
		$user_id = $this->pdh->put('user', 'insert_user_bridge', array($arrUserdata['name'], $strPwdHash.':'.$salt, $arrUserdata['email'], false, $strApiKey));
		if ($user_id){
			$special_users = unserialize(stripslashes($this->config->get('special_user')));
			$special_users[$user_id] = $user_id;
			$this->config->set('special_user', serialize($special_users));
		}
	}
  }

  /**
    * pre_uninstall
    * Define uninstallation
    */
  public function pre_uninstall()
  {
    // include SQL data for uninstallation
    include($this->root_path.'plugins/guildrequest/includes/sql.php');

    for ($i = 1; $i <= count($guildrequestSQL['uninstall']); $i++)
      $this->add_sql(SQL_UNINSTALL, $guildrequestSQL['uninstall'][$i]);
  }

  /**
    * post_uninstall
    * Define Post Uninstall
    */
  public function post_uninstall()
  {
	$user_id = $this->pdh->get('user', 'userid', array('GuildRequest'));
	$special_users = unserialize(stripslashes($this->config->get('special_user')));
	unset($special_users[$user_id]);
	$this->config->set('special_user', serialize($special_users));
  }

  /**
    * gen_admin_menu
    * Generate the Admin Menu
    */
  private function gen_admin_menu()
  {
    $admin_menu = array (array(
        'name' => $this->user->lang('guildrequest'),
        'icon' => './../../plugins/guildrequest/images/adminmenu/guildrequest.png',
        1 => array (
          'link'  => 'plugins/guildrequest/admin/form.php'.$this->SID,
          'text'  => $this->user->lang('gr_manage_form'),
          'check' => 'a_guildrequest_form',
          'icon'  => './../../plugins/guildrequest/images/adminmenu/form.png'
        ),
		/*
		2 => array (
          'link'  => 'plugins/guildrequest/admin/settings.php'.$this->SID,
          'text'  => $this->user->lang('settings'),
          'check' => 'a_guildrequest_settings',
          'icon'  => 'manage_settings.png'
        ),
		*/
    ));

    return $admin_menu;
  }
  
   /**
    * gen_admin_menu
    * Generate the Admin Menu
    */
  private function gen_main_menu()
  {
	
	$main_menu = array(
        1 => array (
          'link'  => 'plugins/guildrequest/addrequest.php'.$this->SID,
          'text'  => $this->user->lang('gr_add'),
          'check' => 'u_guildrequest_add',
		  'signedin' => 0,
        ),
		2 => array (
          'link'  => 'plugins/guildrequest/listrequests.php'.$this->SID,
          'text'  => $this->user->lang('gr_view'),
          'check' => 'u_guildrequest_view',
        ),
    );

    return $main_menu;
  }
  
  private function usersettings(){
	$settings = array(
		'guildrequest' => array(
			'icon' => $this->root_path.'plugins/guildrequest/images/adminmenu/guildrequest.png',
		
		'gr_send_notification_mails'	=> array(
			'fieldtype'	=> 'checkbox',
			'default'	=> 0,
			'name'		=> 'gr_send_notification_mails',
			'language'	=> 'gr_send_notification_mails',
		),
		
		'gr_jgrowl_notifications'	=> array(
			'fieldtype'	=> 'checkbox',
			'default'	=> 0,
			'name'		=> 'gr_jgrowl_notifications',
			'language'	=> 'gr_jgrowl_notifications',
		)),
	);
	return $settings;
  }

}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_guildrequest', guildrequest::__shortcuts());

?>
