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
  | rom_realmstatus
  +--------------------------------------------------------------------------*/
if (!class_exists('rom_realmstatus'))
{
  class rom_realmstatus extends mmo_realmstatus
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
    protected $game_name = 'rom';

    /* URL to load realmstatus from */
    private $base_status_url = 'http://status.blackout-gaming.net/status.php?';

    /* cache time in seconds default 30 minutes = 1800 seconds */
    private $cachetime = 1800;

    /* list of all realms supported with each IP/Port address */
    private $supported_realm_list = array(
      // EU
      'macantacht'      => array('region' => 'EU', 'ip' => '77.95.25.163', 'port' => '16502', 'type' => 'PvE'),
      'siochain'        => array('region' => 'EU', 'ip' => '77.95.25.163', 'port' => '16402', 'type' => 'PvE'),
      'smacht'          => array('region' => 'EU', 'ip' => '77.95.25.164', 'port' => '16402', 'type' => 'PvP'),
      // DE
      'aontacht'        => array('region' => 'DE', 'ip' => '77.95.25.166', 'port' => '16502', 'type' => 'PvE'),
      'laoch'           => array('region' => 'DE', 'ip' => '77.95.25.166', 'port' => '16402', 'type' => 'PvE'),
      'muinin'          => array('region' => 'DE', 'ip' => '77.95.25.167', 'port' => '16502', 'type' => 'PvE'),
      'cogadh'          => array('region' => 'DE', 'ip' => '77.95.25.167', 'port' => '16402', 'type' => 'PvP'),
      'tuath'           => array('region' => 'DE', 'ip' => '77.95.25.164', 'port' => '16502', 'type' => 'PvE'),
      'riocht'          => array('region' => 'DE', 'ip' => '77.95.25.168', 'port' => '16402', 'type' => 'PvE'),
      // US
      'artemis'         => array('region' => 'US', 'ip' => '64.127.104.211', 'port' => '16402', 'type' => 'PvE'),
      'govinda'         => array('region' => 'US', 'ip' => '64.127.104.211', 'port' => '16502', 'type' => 'PvE'),
      'osha'            => array('region' => 'US', 'ip' => '64.127.104.212', 'port' => '16402', 'type' => 'PvE'),
      'grimdal'         => array('region' => 'US', 'ip' => '64.127.104.212', 'port' => '16502', 'type' => 'PvP'),
      'grimdal (krynn)' => array('region' => 'US', 'ip' => '64.127.104.212', 'port' => '16502', 'type' => 'PvP'),
    );

    /* list of login servers */
    private $login_list = array(
      'DE' => array('ip' => '77.95.25.162',   'port' => '21002'),
      'EU' => array('ip' => '77.95.25.162',   'port' => '21002'),
      'US' => array('ip' => '64.127.104.210', 'port' => '21002'),
    );

    /* Array with all realms */
    private $realm_list = array();

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
      $this->image_path = $this->env->link.'portal/realmstatus/rom/images/';

      // read in the realm status
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
      if (is_array($this->realm_list))
      {
        // is in list?
        if (isset($this->realm_list[$servername]))
        {
          // return status
          return $this->realm_list[$servername];
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
      // default output
      $output = '';

      // loop through the servers
      if (is_array($servers))
      {
        $output = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="noborder">';

        foreach($servers as $servername)
        {
          // prepare servername
          $servername = trim($servername);
          $loc_servername = strtolower($servername);

          // header
          $output .= '<tr>';

          // get status
          $status = $this->checkServer($loc_servername);
          switch ($status)
          {
            case 'up':
              $output .= '<td class="rom_realm_status"><img src="'.$this->image_path.'online.png" title="Online"/></td>';
              // append realm name
              $output .= '<td class="rom_realm_name">'.$servername.'</td>';
              // append region
              $region = $this->supported_realm_list[$loc_servername]['region'];
              $output .= '<td class="rom_realm_region"><img src="'.$this->image_path.$region.'.gif" alt="'.$region.'" title="'.$region.'"/></td>';
              // append type
              $realmtype = $this->supported_realm_list[$loc_servername]['type'];
              $output .= '<td class="rom_realm_type">'.$realmtype.'</td>';
              break;
            case 'down':
              $output .= '<td class="rom_realm_status"><img src="'.$this->image_path.'offline.png" title="Offline"/></td>';
              // append realm name
              $output .= '<td class="rom_realm_name">'.$servername.'</td>';
              // append region
              $region = $this->supported_realm_list[$loc_servername]['region'];
              $output .= '<td class="rom_realm_region"><img src="'.$this->image_path.$region.'.gif" alt="'.$region.'" title="'.$region.'"/></td>';
              // append type
              $realmtype = $this->supported_realm_list[$loc_servername]['type'];
              $output .= '<td class="rom_realm_type">'.$realmtype.'</td>';
              break;
            default:
              $output .= '<td colspan="4"><div align="center">'.sprintf($this->user->lang('rs_realm_status_error'), $servername).'</div></td>';
              break;
          }

          // end table row
          $output .= '</tr>';
        }

        $output .= '</table>';
      }

      return $output;
    }

    /**
     * outputCSS
     * Output CSS
     */
    protected function outputCSS()
    {
      $style = '.rom_realm_status {
                  width: 20px;
                  padding: 0px 1px;
                }

                .rom_realm_name {
                  text-align: left;
                  padding: 0px 1px;
                }

                .rom_realm_region {
                  width: 20px;
                  padding: 0px 1px 0px 0px;
                }

                .rom_realm_type {
                  width: 25px;
                  text-align: left;
                }';

      // add css
      $this->tpl->add_css($style);
    }

    /**
     * loadStatus
     * Load status from either the pdc or from website
     */
    private function loadStatus()
    {
      // try to load data from cache
      $this->realm_list = $this->pdc->get('portal.module.realmstatus.rom', false, true);
      if (!$this->realm_list)
      {
        // none in cache or outdated, load from website
        $this->realm_list = $this->loadRealmStatus();
        // store loaded data within cache
        if (is_array($this->realm_list))
        {
          $this->pdc->put('portal.module.realmstatus.rom', $this->realm_list, $this->cachetime, false, true);
        }
      }
    }

    /**
     * loadRealmStatus
     * Load the status for all RunesOfMagic realms
     *
     * @return array(up/down/unknown)
     */
    private function loadRealmStatus()
    {
      // reset output
      $realms = array();

      // set URL reader options
      $this->puf->checkURL_first = true;

      // loop through all supported realms and get status
      foreach ($this->supported_realm_list as $realmname => $realm)
      {
        // build realm url(s)
        $status_url_realm = $this->base_status_url.'dns='.$realm['ip'].'&port='.$realm['port'].'&style=t1';
        $status_url_login = $this->base_status_url.'dns='.$this->login_list[$realm['region']]['ip'].'&port='.$this->login_list[$realm['region']]['port'].'&style=t1';

        // get url content for realm and login
        $url_data_realm = $this->puf->fetch($status_url_realm);
        $url_data_login = $this->puf->fetch($status_url_login);
        if ($url_data_realm && $url_data_login)
        {
          // both, login + realm servers have to be online for "online" status
          if (strstr($url_data_realm, 'online') !== false && strstr($url_data_login, 'online') !== false)
          {
            $status = 'up';
          }
          else {
            $status = 'down';
          }
        }
        else
        {
           $status = 'unknown';
        }

        // add to list of realms
        $realms[$realmname] = $status;
      }

      return $realms;
    }
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_rom_realmstatus', rom_realmstatus::__shortcuts());
?>
