<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date: 2012-08-28 12:00:16 +0200 (Di, 28. Aug 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2010-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 11982 $
 *
 * $Id: status.class.php 11982 2012-08-28 10:00:16Z wallenium $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');
  exit;
}


if (!class_exists('mmo_realmstatus'))
{
  include_once(registry::get_const('root_path').'portal/realmstatus/realmstatus.class.php');
}

/*+----------------------------------------------------------------------------
  | wow_realmstatus
  +--------------------------------------------------------------------------*/
if (!class_exists('wow_realmstatus'))
{
  class wow_realmstatus extends mmo_realmstatus
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('config', 'user', 'game');
      return array_merge(parent::$shortcuts, $shortcuts);
    }

    /* Game name */
    protected $game_name = 'wow';

    /* The style for output */
    private $style;

    /**
     * Constructor
     */
    public function __construct()
    {
      // call base constructor
      parent::__construct();

      // init armory
      $this->initArmory();

      // init the styles class
      $this->initStyle();
    }

    /**
     * checkServer
     * Check if specified server is up/down/unknown
     *
     * @param  string  $servername  Name of server to check
     *
     * @return string ('up', 'down', 'unknown')
     */
    public function checkServer($servername)
    {
      $realmdata = $this->getRealmData($servername);

      // get status of realm
      if (is_array($realmdata) && isset($realmdata['status']))
      {
        switch (intval($realmdata['status']))
        {
          case 0:  return 'down';    break;
          case 1:  return 'up';      break;
          default: return 'unknown'; break;
        }
      }

      return 'unknown';
    }

    /**
     * getOutput
     * Get the portal output for all servers
     *
     * @param  array  $servers  Array of server names
     *
     * @return string
     */
    protected function getOutput($servers)
    {
      // get realms array
      $realms = array();
      foreach ($servers as $realm)
      {
        $realm = trim($realm);
        $realm = html_entity_decode($realm, ENT_QUOTES);
        $realmdata = $this->getRealmData($realm);
        $realms[$realm] = $realmdata;
      }

      // get output from style
      $output = $this->style->output($realms);

      return $output;
    }

    /**
     * outputCSS
     * Output CSS
     */
    protected function outputCSS()
    {
      $this->style->outputCssStyle();
    }

    /**
     * getRealmData
     * Get the realm data for the specified realm
     *
     * @param  string  $realmname  Name of the realm
     *
     * @return array(type, queue, status, population, name, slug)
     */
    private function getRealmData($realmname)
    {
      // convert the realm name to the API specific handling
      $name = trim($realmname);
      $name = strtolower($name);
      $name = str_replace(array('\'', ' '), array('', '-'), $name);

      // get the cached (do not force) realm data for this realm
      $realmdata = $this->game->obj['armory']->realm(array($name), false);

      // the data are returned as array with
      // 'realms' => array(array(type, queue, status, population, name, slug))

      // if array contains more than 1 realm, the realm is unknown and all realms are returned
      // by the API, so ignore them
      if (is_array($realmdata) && isset($realmdata['realms']) && is_array($realmdata['realms']) && count($realmdata['realms']) == 1)
      {
        // extract the realm data for this realm
        return $realmdata['realms'][0];
      }

      // return as unknown
      return array(
        'type'       => 'error',
        'queue'      => '',
        'status'     => -1,
        'population' => 'error',
        'name'       => $realmname,
        'slug'       => $name,
      );
    }

    /**
     * initArmory
     * Initialize the Armory access
     */
    private function initArmory()
    {
      // init the Battle.net armory object
      $serverLoc = $this->config->get('uc_server_loc') ? $this->config->get('uc_server_loc') : 'eu';
      $this->game->new_object('bnet_armory', 'armory', array($serverLoc, $this->config->get('uc_data_lang')));
    }

    /**
     * initStyle
     * Initialize the styles classes
     */
    private function initStyle()
    {
      $file_style_normal = $this->root_path.'portal/realmstatus/wow/styles/wowstatus.style_normal.class.php';
      $file_style_gdi    = $this->root_path.'portal/realmstatus/wow/styles/wowstatus.style_gdi.class.php';

      // include the files
      include_once($file_style_normal);
      include_once($file_style_gdi);

      // get class
      if ($this->config->get('rs_gd'))
        $this->style = registry::register('wowstatus_style_gdi');
      else
        $this->style = registry::register('wowstatus_style_normal');
    }

  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_wow_realmstatus', wow_realmstatus::__shortcuts());
?>
