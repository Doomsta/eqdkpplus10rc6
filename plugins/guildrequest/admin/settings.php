<?php
/*
 * Project:     EQdkp guildrequest
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-10-13 22:48:23 +0200 (Sa, 13. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest
 * @version     $Rev: 12273 $
 *
 * $Id: settings.php 12273 2012-10-13 20:48:23Z godmod $
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'guildrequest');

$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path.'common.php');


/*+----------------------------------------------------------------------------
  | guildrequestSettings
  +--------------------------------------------------------------------------*/
class guildrequestSettings extends page_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('pm', 'user', 'config', 'core', 'in', 'jquery', 'html', 'tpl');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('guildrequest', PLUGIN_INSTALLED))
      message_die($this->user->lang('gr_plugin_not_installed'));

    $handler = array(
      'sb_save' => array('process' => 'save', 'csrf' => true, 'check' => 'a_guildrequest_manage'),
    );
    parent::__construct('a_guildrequest_manage', $handler);

    $this->process();
  }

  /**
   * save
   * Save the configuration
   */
  public function save()
  {

    // take over new values
    $savearray = array(
      'sb_use_users' => $this->in->get('sb_use_users', 0),
    );

    // update configuration
    $this->config->set($savearray, '', 'guildrequest');
    // Success message
    $messages[] = $this->user->lang('sb_config_saved');

    $this->display($messages);
  }

  /**
   * display
   * Display the page
   *
   * @param    array  $messages   Array of Messages to output
   */
  public function display($messages=array())
  {
    // -- Messages ------------------------------------------------------------
    if ($messages)
    {
      foreach($messages as $name)
        $this->core->message($name, $this->user->lang('guildrequest'), 'green');
    }

    // -- Template ------------------------------------------------------------
    $this->jquery->Dialog('Aboutguildrequest', $this->user->lang('sb_about_header'), array('url'=>'../about.php', 'width'=>'400', 'height'=>'250'));
    $this->tpl->assign_vars(array (
      // form
      'F_USE_USERS'       => $this->html->CheckBox('sb_use_users', '', $this->config->get('sb_use_users', 'guildrequest')),

      // credits
      'SB_INFO_IMG'       => '../images/credits/info.png',
      'L_CREDITS'         => $this->user->lang('sb_credits_part1').$this->pm->get_data('guildrequest', 'version').$this->user->lang('sb_credits_part2'),
    ));


    // -- EQDKP ---------------------------------------------------------------
    $this->core->set_vars(array(
      'page_title'    => $this->user->lang('guildrequest').' '.$this->user->lang('settings'),
      'template_path' => $this->pm->get_data('guildrequest', 'template_path'),
      'template_file' => 'admin/settings.html',
      'display'       => true
    ));
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_guildrequestSettings', guildrequestSettings::__shortcuts());
registry::register('guildrequestSettings');

?>
