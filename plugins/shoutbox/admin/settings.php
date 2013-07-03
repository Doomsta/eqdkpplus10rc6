<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-10-13 22:48:23 +0200 (Sa, 13. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 12273 $
 *
 * $Id: settings.php 12273 2012-10-13 20:48:23Z godmod $
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'shoutbox');

$eqdkp_root_path = './../../../';
include_once('./../includes/common.php');


/*+----------------------------------------------------------------------------
  | ShoutboxSettings
  +--------------------------------------------------------------------------*/
class ShoutboxSettings extends page_generic
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
    if (!$this->pm->check('shoutbox', PLUGIN_INSTALLED))
      message_die($this->user->lang('sb_plugin_not_installed'));

    $handler = array(
      'sb_save' => array('process' => 'save', 'csrf' => true, 'check' => 'a_shoutbox_'),
    );
    parent::__construct('a_shoutbox_', $handler);

    $this->process();
  }

  /**
   * save
   * Save the configuration
   */
  public function save()
  {
    // is use_user change?
    if ($this->in->get('sb_use_users', 0) != $this->config->get('sb_use_users', 'shoutbox'))
    {
      $shoutbox = registry::register('ShoutboxClass');

      // convert to member?
      if ($this->in->get('sb_use_users', '0') == '1')
      {
        $shoutbox->convertFromMemberToUser();
        $messages[] = $this->user->lang('sb_convert_member_user_success');
      }
      else
      {
        $shoutbox->deleteAllEntries();
        $messages[] = $this->user->lang('sb_convert_user_member_success');
      }
    }

    // take over new values
    $savearray = array(
      'sb_use_users' => $this->in->get('sb_use_users', 0),
    );

    // update configuration
    $this->config->set($savearray, '', 'shoutbox');
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
        $this->core->message($name, $this->user->lang('shoutbox'), 'green');
    }

    // -- Template ------------------------------------------------------------
    $this->jquery->Dialog('AboutShoutbox', $this->user->lang('sb_about_header'), array('url'=>'../about.php', 'width'=>'400', 'height'=>'250'));
    $this->tpl->assign_vars(array (
      // form
      'F_USE_USERS'       => $this->html->CheckBox('sb_use_users', '', $this->config->get('sb_use_users', 'shoutbox')),

      // credits
      'SB_INFO_IMG'       => '../images/credits/info.png',
      'L_CREDITS'         => $this->user->lang('sb_credits_part1').$this->pm->get_data('shoutbox', 'version').$this->user->lang('sb_credits_part2'),
    ));


    // -- EQDKP ---------------------------------------------------------------
    $this->core->set_vars(array(
      'page_title'    => $this->user->lang('shoutbox').' '.$this->user->lang('settings'),
      'template_path' => $this->pm->get_data('shoutbox', 'template_path'),
      'template_file' => 'admin/settings.html',
      'display'       => true
    ));
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ShoutboxSettings', ShoutboxSettings::__shortcuts());
registry::register('ShoutboxSettings');

?>
