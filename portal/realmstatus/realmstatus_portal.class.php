<?php
 /*
 * Project:   EQdkp-Plus
 * License:   Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:   2008
 * Date:    $Date: 2012-02-19 13:19:34 +0100 (So, 19. Feb 2012) $
 * -----------------------------------------------------------------------
 * @author    $Author: Aderyn $
 * @copyright 2008-2011 Aderyn
 * @link    http://eqdkp-plus.com
 * @package   eqdkp-plus
 * @version   $Rev: 11694 $
 *
 * $Id: realmstatus_portal.class.php 11694 2012-02-19 12:19:34Z Aderyn $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

/*+----------------------------------------------------------------------------
  | realmstatus_portal
  +--------------------------------------------------------------------------*/
class realmstatus_portal extends portal_generic
{

  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('user', 'pdc', 'game');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  protected $path = 'realmstatus';
  protected $data = array(
    'name'        => 'Realmstatus Module',
    'version'     => '1.1.3',
    'author'      => 'Aderyn',
    'contact'     => 'Aderyn@gmx.net',
    'description' => 'Show Realmstatus',
  );
  protected $positions = array('left1', 'left2', 'right');
  protected $settings = array(
    'pk_realmstatus_realm'  => array(
      'name'     => 'rs_realm',
      'language' => 'rs_realm',
      'property' => 'text',
      'size'     => '40',
      'help'     => 'rs_realm_help',
    ),
    'pk_realmstatus_us' => array(
      'name'     => 'rs_us',
      'language' => 'rs_us',
      'property' => 'checkbox',
      'help'     => 'rs_us_help',
    ),
  );
  protected $install  = array(
    'autoenable'      => '0',
    'defaultposition' => 'right',
    'defaultnumber'   => '5',
  );
  protected $exchangeModules = array(
    'realmstatus',
  );

  /**
   * Constructor
   */
  public function __construct($position='')
  {
    parent::__construct($position);

    // check ig gd lib is available, if so, make option to use available
    if (extension_loaded('gd') && function_exists('gd_info'))
    {
      $this->settings['pk_realmstatus_gd'] = array(
        'name'     => 'rs_gd',
        'language' => 'rs_gd',
        'property' => 'checkbox',
        'text'     => 'GD LIB Version',
        'help'     => 'rs_gd_help',
      );
    }
  }

  /**
   * output
   * Returns the portal output
   *
   * @return string
   */
  public function output()
  {
    // empty output as default
    $realmstatus = '';

    // try to load the status file for this game
    $game_name = strtolower($this->game->get_game());
    $status_file = $this->root_path.'portal/realmstatus/'.$game_name.'/status.class.php';
    if (file_exists($status_file))
    {
      include_once($status_file);

      $class_name = $game_name.'_realmstatus';
      $status = registry::register($class_name);
      if ($status)
        $realmstatus .= $status->getPortalOutput();
      else
        $realmstatus .= '<div class="center">'.$this->user->lang('rs_game_not_supported').'</div>';
    }
    else
    {
      $realmstatus .= '<div class="center">'.$this->user->lang('rs_game_not_supported').'</div>';
    }

    // return the output for module manager
    return $realmstatus;
  }

  /**
   * reset
   * Reset the portal module
   */
  public function reset()
  {
    // clear cache
    $this->pdc->del_prefix('portal.module.realmstatus');
  }

}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_realmstatus_portal', realmstatus_portal::__shortcuts());
?>
