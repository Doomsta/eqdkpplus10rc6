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
  | swtor_realmstatus
  +--------------------------------------------------------------------------*/
if (!class_exists('swtor_realmstatus'))
{
  class swtor_realmstatus extends mmo_realmstatus
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
    protected $game_name = 'swtor';

    /* URL to load serverstatus from */
    private $status_url = 'http://www.swtor.com/server-status';

    /* cache time in seconds default 30 minutes = 1800 seconds */
    private $cachetime = 1800;

    /* Array with all servers */
    private $server_list = array();

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
      $this->image_path = $this->env->link.'portal/realmstatus/swtor/images/';

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

      // loop through the servers and collect server data
      $swtor_servers = array();
      if (is_array($servers))
      {
        foreach($servers as $servername)
        {
          // get server data
          $servername = trim($servername);
          $servername = html_entity_decode($servername, ENT_QUOTES);
          $serverdata = $this->getServerData($servername);

          // get status
          switch (strtolower($serverdata['status']))
          {
          case 'up':      $status = 'online';  break;
          case 'down':    $status = 'offline'; break;
          case 'booting': $status = 'booting'; break;
          default:        $status = 'unknown'; break;
          }

          // get server type
          if ($serverdata['type'] == '')
          {
            $type = $this->user->lang('rs_unknown');
            $country_div = '';
          }
          else
          {
            $type = $serverdata['type'];

             // set country
             $country_flag = $this->getCountryFlag($serverdata['language']);
             $country_title = $serverdata['region'] == 'us' ? $serverdata['language'].' ('.$serverdata['timezone'].')' : $serverdata['language'];
             $country_div = '<img src="'.$this->env->link.'images/flags/'.$country_flag.'.png" alt="'.$country_title.'" title="'.$country_title.'"/>';
          }

          $output .= '<div class="rs_swtor_status rs_swtor_'.$status.'">
                        <div class="rs_swtor_detail roundbox">
                          <div class="rs_swtor_country">'.$country_div.'</div>
                          <div class="rs_swtor_type">'.$type.'</div>
                          <div class="rs_swtor_name nowrap">'.$servername.'</div>
                        </div>
                      </div>';
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
      $style = '.rs_swtor_status {
                   padding-left:  10px;
                   padding-top:   159px;
                   padding-right: 10px;
                   height:        41px;
                   margin-top:    2px;
                   margin-bottom: 2px;
                 }

                 .rs_swtor_online {
                   background: url("'.$this->image_path.'online.png") no-repeat scroll 0 0 transparent;
                 }

                 .rs_swtor_offline {
                   background: url("'.$this->image_path.'offline.png") no-repeat scroll 0 0 transparent;
                 }

                 .rs_swtor_booting {
                   background: url("'.$this->image_path.'booting.png") no-repeat scroll 0 0 transparent;
                 }

                 .rs_swtor_unknown {
                   background: url("'.$this->image_path.'searching.png") no-repeat scroll 0 0 transparent;
                 }

                 .rs_swtor_detail {
                   background: none repeat scroll 0 0 rgba(10, 10, 10, 0.6);
                   border:     solid 1px #777777;
                   padding:    5px 10px;
                   margin:     3px;
                 }

                 .rs_swtor_country, .rs_swtor_type {
                   display: inline;
                 }

                 .rs_swtor_name {
                   margin-top: 1px;
                 }';

      // add css
      $this->tpl->add_css($style);
    }

    /**
     * getServerData
     * Gets the data for the specified server
     *
     * @param  string  $servername  Name of the server to get data of
     *
     * @return array(status, population, type, timezone, language, region)
     */
    private function getServerData($servername)
    {
      $name = trim($servername);

      if (isset($this->server_list[$name]))
        return $this->server_list[$name];

      return array(
        'status'     => 'unknown',
        'population' => -1,
        'type'       => '',
        'timezone'   => 'unknown',
        'language'   => 'unknown',
        'region'     => 'unknown',
      );
    }

    /**
     * loadStatus
     * Load status from either the pdc or from website
     */
    private function loadStatus()
    {
      // try to load data from cache
      $this->server_list = $this->pdc->get('portal.module.realmstatus.swtor', false, true);
      if (!$this->server_list)
      {
        // none in cache or outdated, load from website
        $this->server_list = $this->loadServerStatus();
        // store loaded data within cache
        if (is_array($this->server_list))
        {
          $this->pdc->put('portal.module.realmstatus.swtor', $this->server_list, $this->cachetime, false, true);
        }
      }
    }

    /**
     * loadServerStatus
     * Load the status for all Star Wars The Old Republic servers
     *
     * @return array(status, population, type, timezone, language, region)
     */
    private function loadServerStatus()
    {
      // reset output
      $servers = array();

      // set URL reader options
      $this->puf->checkURL_first = true;

      // load html page
      $html = $this->puf->fetch($this->status_url);
      if (!$html || empty($html))
        return $servers;

      // create new swtor html class
      require_once($this->root_path.'portal/realmstatus/swtor/swtor_html.class.php');
      $swtor_html = new swtor_html($html);

      // get the server lists
      $server_list_us = $swtor_html->getServerListUS();
      $server_list_eu = $swtor_html->getServerListEU();

      if (!$server_list_us || !$server_list_eu)
        return $servers;

      // process the server lists
      $servers_us = $server_list_us->getServers();
      if (is_array($servers_us))
      {
        foreach ($servers_us as $server)
        {
          $servers[$server->name] = array(
            'status'     => $server->status,
            'population' => intval($server->population),
            'type'       => $server->type,
            'timezone'   => $server->timezone,
            'language'   => 'US',
            'region'     => $server->region,
          );
        }
      }

      $servers_eu = $server_list_eu->getServers();
      if (is_array($servers_eu))
      {
        foreach ($servers_eu as $server)
        {
          $servers[$server->name] = array(
            'status'     => $server->status,
            'population' => intval($server->population),
            'type'       => $server->type,
            'timezone'   => '',
            'language'   => $server->language,
            'region'     => $server->region,
            );
        }
      }

      // cleanup memory
      $swtor_html->clear();

      return $servers;
    }

    /**
     * getCountryFlag
     * Gets the country flag image
     *
     * @param  string  $server_language  Language of server
     *
     * @return  string
     */
    private function getCountryFlag($server_language)
    {
        // return pvp status
      $language = strtolower($server_language);
      switch ($language)
      {
      case 'german':  return 'de';
      case 'english': return 'gb';
      case 'french':  return 'fr';
      case 'us':      return 'us';
      }

      return '';
    }
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_swtor_realmstatus', swtor_realmstatus::__shortcuts());
?>
