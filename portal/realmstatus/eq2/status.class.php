<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date: 2012-06-05 17:53:50 +0200 (Di, 05. Jun 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: shoorty $
 * @copyright   2010-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 11807 $
 *
 * $Id: status.class.php 11807 2012-06-05 15:53:50Z shoorty $
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
  | eq2_realmstatus
  +--------------------------------------------------------------------------*/
if (!class_exists('eq2_realmstatus'))
{
  class eq2_realmstatus extends mmo_realmstatus
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('user', 'pdc', 'puf' => 'urlfetcher', 'env' => 'environment', 'tpl');
      return array_merge(parent::$shortcuts, $shortcuts);
    }

    /* Game name */
    protected $game_name = 'eq2';

    /* URL to load server status from */
    private $eq2_url = 'http://data.soe.com/xml/status/eq2';

    /* cache time in seconds default 10 minutes = 600 seconds */
    private $cachetime = 600;

    /* Array with all servers */
    private $servers = array();

    /* image path */
    private $image_path;


    /**
     * Constructor
     */
    public function __construct()
    {
      // call base constructor
      parent::__construct();

      // set image path
      $this->image_path = $this->env->link.'portal/realmstatus/eq2/images/';

      // read in the server status
      $this->loadStatus();
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
      if (is_array($this->servers))
      {
        // is in list?
        if (isset($this->servers[$servername]))
        {
          // return status
          switch ($this->servers[$servername]['status'])
          {
            case 'down':    return 'down';
            case 'locked':  return 'up';
            default:        return 'up';
          }
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
      // set output
      $output = '';

      // loop through the servers
      if (is_array($servers))
      {
        foreach($servers as $servername)
        {
          // get status
          $servername = trim($servername);
          $status = $this->checkServer($servername);

          // output
          $output .= '<div class="tr">';

          // output status
          switch ($status)
          {
            case 'up':
              $output .= '<div class="td"><img src="'.$this->image_path.'up.png" alt="Online" title="'.$servername.'" /></div>';
              break;
            case 'down':
              $output .= '<div class="td"><img src="'.$this->image_path.'down.png" alt="Offline" title="'.$servername.'" /></div>';
              break;
            default:
              $output .= '<div class="td"><img src="'.$this->image_path.'down.png" alt="'.$this->user->lang('rs_unknown').'" title="'.$servername.' ('.$this->user->lang('rs_unknown').')" /></div>';
              break;
          }

          // output server name
          $output .= '<div class="td">'.$servername.'</div>';

          // output country flag
          $country_flag = $this->getCountryFlag($servername);
          $output .= '<div class="td">';
          if ($country_flag != '')
            $output .= '<img src="'.$this->env->link.'images/flags/'.$country_flag.'.png" alt="'.$country_flag.'" title="'.$this->servers[$servername]['region'].'"/>';
          $output .= '</div>';

          // end row diff
          $output .= '</div>';
        }
      }

      return $output;
    }

    /**
     * outputCSS
     * Output CSS
     */
    protected function outputCSS()
    {
    }

    /**
     * loadStatus
     * Load status from either the pdc or from website
     */
    private function loadStatus()
    {
      // try to load data from cache
      $this->servers = $this->pdc->get('portal.module.realmstatus.eq2', false, true);
      if ($this->servers === null)
      {
        // none in cache or outdated, load from website
        $this->servers = $this->loadServers();
        // store loaded data within cache
        if (is_array($this->servers))
        {
          $this->pdc->put('portal.module.realmstatus.eq2', $this->servers, $this->cachetime, false, true);
        }
      }
    }

    /**
     * loadShards
     * Load the servers from the SOE website
     *
     * @return array
     */
    private function loadServers()
    {
      // reset output
      $servers = array();

      // set URL reader options
      $this->puf->checkURL_first = true;

      // load xml
      $xml_string = $this->puf->fetch($this->eq2_url);
      if ($xml_string)
      {
        // parse xml
        $xml = simplexml_load_string($xml_string);
        if ($xml && $xml->game)
        {
          foreach ($xml->game->region as $region)
          {
            foreach ($region->server as $server)
            {
              $attributes = $server->attributes();
              $servers[(string)$attributes->name] = array(
                'region' => (string)$region->attributes()->name,
                'status' => (string)$attributes->status,
              );
            }
          }
        }
      }

      return $servers;
    }

    /**
     * getCountryFlag
     * Get the country flag for shard
     *
     * @param  string  $servername  Name of server to get flag of
     *
     * @return string
     */
    private function getCountryFlag($servername)
    {
      if (is_array($this->servers))
      {
        // is in list?
        if (isset($this->servers[$servername]))
        {
          // return country
          $region = $this->servers[$servername]['region'];
          if (strcmp($region, 'EU Deutsch') == 0)  return 'de';
          if (strcmp($region, 'EU English') == 0)  return 'gb';
          if (strcmp($region, 'EU Français') == 0) return 'fr';
          if (strcmp($region, 'US English') == 0)  return 'us';
          if (strcmp($region, 'Русский') == 0)     return 'ru';
          if (strcmp($region, '日本語') == 0)      return 'cn';
        }
      }

      return '';
    }
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_eq2_realmstatus', eq2_realmstatus::__shortcuts());
?>
