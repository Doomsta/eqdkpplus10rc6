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
  | lotro_realmstatus
  +--------------------------------------------------------------------------*/
if (!class_exists('lotro_realmstatus'))
{
  class lotro_realmstatus extends mmo_realmstatus
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
    protected $game_name = 'lotro';

    /* Have a look at
     * http://forums.lotro.com/showthread.php?334025-LOTRO-Server-Status-v2.0
     * for details of this class
     */

    /* URL to load realmstatus from */
    private $lotro_url = 'http://lux-hdro.de/serverstatus-rss.php?';

    /* cache time in seconds default 10 minutes = 600 seconds */
    private $cachetime = 600;

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
      $this->image_path = $this->env->link.'portal/realmstatus/lotro/images/';
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
      // try to load xml string from cache
      $status = $this->pdc->get('portal.module.realmstatus.lotro.'.$servername, false, true);
      if ($status === null)
      {
        // none in cache or outdated, load from website
        $status = $this->loadStatus($servername);
        if ($status !== false)
        {
          // store loaded data within cache
          $this->pdc->put('portal.module.realmstatus.lotro.'.$servername, $status, $this->cachetime, false, true);
        }
        else
        {
          $status = 'unknown';
        }
      }

      return $status;
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
      // header
      $output = '<div class="tr"><img src="'.$this->image_path.'status-head.gif"/></div>';

      // loop through the servers
      if (is_array($servers))
      {
        foreach($servers as $servername)
        {
          $servername = trim($servername);
          $status = $this->checkServer($servername);
          switch ($status)
          {
            case 'up':
              $output .= '<div class="rs_server_up">'.$servername.'</div>';
              break;
            case 'down':
              $output .= '<div class="rs_server_down">'.$servername.'</div>';
              break;
            default:
              $output .= '<div class="rs_server_down">'.$servername.' ('.$this->user->lang('rs_unknown').')</div>';
              break;
          }
        }
      }

      // footer
      $output .= '<div class="tr"><img src="'.$this->image_path.'status_base.gif"/></div>';

      return $output;
    }

    /**
     * outputCSS
     * Output CSS
     */
    protected function outputCSS()
    {
      $style = '.rs_server_up {
                  background-image:url(\''.$this->image_path.'server-on.gif\');
                  width:145px;
                  height:25px;
                  padding-left:55px;
                  padding-top:10px;
                  margin: 0px;
                  font-family: Verdana, Arial, Helvetica, sans-serif;
                  font-size: 11px;
                  color: #fff;
                }

                .rs_server_down {
                  background-image:url(\''.$this->image_path.'server-off.gif\');
                  width:145px;
                  height:25px;
                  padding-left:55px;
                  padding-top:10px;
                  margin: 0px;
                  font-family: Verdana, Arial, Helvetica, sans-serif;
                  font-size: 11px;
                  color: #fff;
                }

                .rs_server_middle {
                  background-image:url(\''.$this->image_path.'server-middle.gif\');
                  width:145px;
                  height:25px;
                  padding-left:55px;
                  padding-top:10px;
                  margin: 0px;
                  font-family: Verdana, Arial, Helvetica, sans-serif;
                  font-size: 11px;
                  color: #fff;
                }';

      // add css
      $this->tpl->add_css($style);
    }

    /**
     * loadStatus
     * Load status from either the pdc or from codemasters website
     *
     * @param  string  $servername  Name of server to check
     *
     * @return string ('up', 'down', 'unknown')
     */
    private function loadStatus($servername)
    {
      $this->puf->checkURL_first = true;
      $xml_string = $this->puf->fetch($this->lotro_url.urlencode(utf8_strtolower($servername)).'=1');
      if ($xml_string)
        return $this->parseXML($xml_string, $servername);

      return 'unknown';
    }

    /**
     * parseXML
     * Parse the XML realm string
     *
     * @param  string  $xml_string  Content of Status XML
     *
     * @return string ('up', 'down', 'unknown')
     */
    private function parseXML($xml_string, $servername)
    {
      if (!empty($xml_string))
      {
        // parse xml		
        $xml = simplexml_load_string($xml_string);
        if ($xml !== false && $xml->channel->item)
        {		
			foreach($xml->channel->item as $item){
				$strDesc = $item->description;
				if (strpos($strDesc, $servername) === 0){
					$string = substr($strDesc, strlen($servername)+2);
					if (strpos($string, 'offen') === 0) return "up";
					if (strpos($string, 'zu') === 0) return "down";
				}
			}
        }
      }

      return 'unknown';
    }
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_lotro_realmstatus', lotro_realmstatus::__shortcuts());
?>
