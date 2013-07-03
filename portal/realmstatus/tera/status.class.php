<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date: 2012-12-05 10:21:02 +0100 (Mi, 05. Dez 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2010-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 12565 $
 *
 * $Id: status.class.php 12565 2012-12-05 09:21:02Z godmod $
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
  | tera_realmstatus
  +--------------------------------------------------------------------------*/
if (!class_exists('tera_realmstatus'))
{
  class tera_realmstatus extends mmo_realmstatus
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
    protected $game_name = 'tera';

    /* URL to load serverstatus from */
    private $status_url = 'http://tera-europe.com/server-status.html';

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
      $this->image_path = $this->env->link.'portal/realmstatus/tera/images/';

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
      $servername = trim($servername);
      $servername = html_entity_decode($servername, ENT_QUOTES);
      $serverdata = $this->getServerData($servername);

      switch ($serverdata['status'])
      {
        case 'online':  return 'up';
        case 'offline': return 'down';
        default:        return 'unknown';
      }
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
      $tera_servers = array();
      if (is_array($servers))
      {
        foreach($servers as $servername)
        {
          // get server data
          $servername = trim($servername);
          $servername = html_entity_decode($servername, ENT_QUOTES);
          $serverdata = $this->getServerData($servername);

          // output
          $output .= '<div class="tr">';

          // output status
          switch ($serverdata['status'])
          {
            case 'online':
              $output .= '<div class="td"><img src="'.$this->image_path.'server_on.png" alt="Online" title="'.$servername.'" /></div>';
              $isUnknown = false;
              break;
            case 'offline':
              $output .= '<div class="td"><img src="'.$this->image_path.'server_off.png" alt="Offline" title="'.$servername.'" /></div>';
              $isUnknown = false;
              break;
            default:
              $output .= '<div class="td"><img src="'.$this->image_path.'server_off.png" alt="Unknown" title="'.$servername.' ('.$this->user->lang('rs_unknown').')" /></div>';
              $isUnknown = true;
              break;
          }

          // output server name
          $output .= '<div class="td">'.$servername.'</div>';

          // output server type
          if ($isUnknown)
          {
            $output .= '<div class="td"></div>';
          }
          else
          {
            if ($serverdata['type'] == 'PVP')
                $output .= '<div class="td rs_tera_pvp">PVP</div>';
            else
                $output .= '<div class="td rs_tera_pve">PVE</div>';
          }

          // output country flag
          $country_flag = $this->getCountryFlag($serverdata['language']);
          $output .= '<div class="td">';
          if ($country_flag != '')
            $output .= '<img src="'.$this->env->link.'images/flags/'.$country_flag.'.png" alt="'.$country_flag.'" title="'.$serverdata['language'].'"/>';
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
      $style = '.rs_tera_pve {
                  color: #739EFF;
                }

                .rs_tera_pvp {
                  color: #FF7373;
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
     * @return array(status, population, type, language)
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
        'language'   => 'unknown',
      );
    }

    /**
     * loadStatus
     * Load status from either the pdc or from website
     */
    private function loadStatus()
    {
      // try to load data from cache
      $this->server_list = $this->pdc->get('portal.module.realmstatus.tera', false, true);
      if (!$this->server_list)
      {
        // none in cache or outdated, load from website
        $this->server_list = $this->loadServerStatus();
        // store loaded data within cache
        if (is_array($this->server_list))
        {
          $this->pdc->put('portal.module.realmstatus.tera', $this->server_list, $this->cachetime, false, true);
        }
      }
    }

    /**
     * loadServerStatus
     * Load the status for all TERA servers
     *
     * @return array(status, population, type, language)
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

      // create new tera html class
      require_once($this->root_path.'portal/realmstatus/tera/tera_html.class.php');
      $tera_html = new tera_html($html);

      // get the server lists
      $server_list = $tera_html->getServerList();

      if (!$server_list)
        return $servers;

      // process the server lists
      $tera_servers = $server_list->getServers();
      if (is_array($tera_servers))
      {
        foreach ($tera_servers as $server)
        {
          $servers[$server->name] = array(
            'status'     => $server->status,
            'population' => intval($server->population),
            'type'       => $server->type,
            'language'   => $server->language,
          );
        }
      }

      // cleanup memory
      $tera_html->clear();

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
        // return country status
      $language = strtolower($server_language);
      switch ($language)
      {
      case 'de': return 'de';
      case 'en': return 'gb';
      case 'fr': return 'fr';
      }

      return '';
    }
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_tera_realmstatus', tera_realmstatus::__shortcuts());
?>
