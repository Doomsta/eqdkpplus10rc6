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
  | rift_realmstatus
  +--------------------------------------------------------------------------*/
if (!class_exists('rift_realmstatus'))
{
  class rift_realmstatus extends mmo_realmstatus
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('user', 'config', 'pdc', 'puf' => 'urlfetcher', 'env' => 'environment', 'tpl');
      return array_merge(parent::$shortcuts, $shortcuts);
    }

    /* Game name */
    protected $game_name = 'rift';

    /* URL to load shard status from (EU)*/
    private $rift_url_eu = 'http://eu.riftgame.com/en/status/eu-status.xml';

    /* URL to load shard status from (North America)*/
    private $rift_url_us = 'http://eu.riftgame.com/en/status/na-status.xml';

    /* cache time in seconds default 10 minutes = 600 seconds */
    private $cachetime = 600;

    /* Array with all shards */
    private $shards = array();

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
      $this->image_path = $this->env->link.'portal/realmstatus/rift/images/';

      // read in the shard status
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
      if (is_array($this->shards))
      {
        // is in list?
        if (isset($this->shards[$servername]))
        {
          // return status
          return $this->shards[$servername]["online"] ? 'up' : 'down';
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
              $output .= '<div class="td"><img src="'.$this->image_path.'shard_status_online.png" alt="Online" title="'.$servername.'" /></div>';
              break;
            case 'down':
              $output .= '<div class="td"><img src="'.$this->image_path.'shard_status_offline.png" alt="Offline" title="'.$servername.'" /></div>';
              break;
            default:
              $output .= '<div class="td"><img src="'.$this->image_path.'shard_status_offline.png" alt="Offline" title="'.$servername.' ('.$this->user->lang('rs_unknown').')" /></div>';
              break;
          }

          // output server name
          $output .= '<div class="td">'.$servername.'</div>';

          // output server type
          if ($this->isServerPvP($servername))
            $output .= '<div class="td rs_rift_pvp">PvP';
          else
            $output .= '<div class="td rs_rift_pve">PvE';

          // append RP flag
          if ($this->isServerRP($servername))
            $output .= ' <div>RP</div>';

          // end diff from server type
          $output .= '</div>';

          // output country flag
          $country_flag = $this->getCountryFlag($servername);
          $output .= '<div class="td">';
          if ($country_flag != '')
            $output .= '<img src="'.$this->env->link.'images/flags/'.$country_flag.'.png" alt="'.$country_flag.'" title="'.$this->shards[$servername]['language'].'"/>';
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
      $style = '.rs_rift_pve {
                  color: #F2CE85;
                }

                .rs_rift_pvp {
                  color: #E51717;
                }

                .rs_rift_pve > div, .rs_rift_pvp > div {
                  color:   #8936B3;
                  display: inline;
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
      // get region
      $region = ($this->config->get('rs_us')) ? 'us' : 'eu';

      // try to load data from cache
      $this->shards = $this->pdc->get('portal.module.realmstatus.rift.'.$region, false, true);
      if ($this->shards === null)
      {
        // none in cache or outdated, load from website
        $this->shards = $this->loadShards();
        // store loaded data within cache
        if (is_array($this->shards))
        {
          $this->pdc->put('portal.module.realmstatus.rift.'.$region, $this->shards, $this->cachetime, false, true);
        }
      }
    }

    /**
     * loadShards
     * Load the shards from the RIFT website
     *
     * @return array
     */
    private function loadShards()
    {
      // reset output
      $shards = array();

      // get url depending on region
      $shards_url = ($this->config->get('rs_us')) ? $this->rift_url_us : $this->rift_url_eu;

      // set URL reader options
      $this->puf->checkURL_first = true;

      // load xml
      $xml_string = $this->puf->fetch($shards_url);
      if ($xml_string)
      {
        // parse xml
        $xml = simplexml_load_string($xml_string);
        if ($xml)
        {
          foreach ($xml->shard as $shard)
          {
            $attributes = $shard->attributes();
            $shards[(string)$attributes->name] = array(
              'online'   => ((string)$attributes->online == 'True') ? true : false,
              'language' => (string)$attributes->language,
              'pvp'      => ((string)$attributes->pvp == 'True') ? true : false,
              'rp'       => ((string)$attributes->rp == 'True') ? true : false,
            );
          }
        }
      }

      return $shards;
    }

    /**
     * isServerPvP
     * Get the server type PvE/PvP
     *
     * @param  string  $servername  Name of server to get type of
     *
     * @return boolen
     */
    private function isServerPvP($servername)
    {
      if (is_array($this->shards))
      {
        // is in list?
        if (isset($this->shards[$servername]))
        {
          // return pvp status
          return $this->shards[$servername]["pvp"];
        }
      }

      return false;
    }

    /**
     * isServerRP
     * Get the server RP
     *
     * @param  string  $servername  Name of server to get RP of
     *
     * @return boolen
     */
    private function isServerRP($servername)
    {
      if (is_array($this->shards))
      {
        // is in list?
        if (isset($this->shards[$servername]))
        {
          // return pvp status
          return $this->shards[$servername]["rp"];
        }
      }

      return false;
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
      if (is_array($this->shards))
      {
        // is in list?
        if (isset($this->shards[$servername]))
        {
          // return pvp status
          $language = strtolower($this->shards[$servername]["language"]);
          switch ($language)
          {
            case 'german':  return 'de';
            case 'english': return $this->config->get('rs_us') ? 'us' : 'gb';
            case 'french':  return 'fr';
          }
        }
      }

      return '';
    }
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_rift_realmstatus', rift_realmstatus::__shortcuts());
?>
