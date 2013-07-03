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
 * $Id: swtor_html.class.php 11807 2012-06-05 15:53:50Z shoorty $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');
  exit;
}


if (!class_exists('simple_html_dom'))
{
  include_once(registry::get_const('root_path').'portal/realmstatus/includes/simple_html_dom.php');
}


/*+----------------------------------------------------------------------------
  | swtor_html
  +--------------------------------------------------------------------------*/
if (!class_exists('swtor_html'))
{
  class swtor_html
  {
    /* The US server list */
    private $swtor_servers_us = null;

    /* The EU server list */
    private $swtor_servers_eu = null;

    /* The AP server list */
    private $swtor_servers_ap = null;

    /* The DOM object */
    private $dom;

    /**
     * Constructor
     *
     * @param  string  $html  HTML data of the SWTOR status page
     */
    public function __construct($html)
    {
      $this->dom = new simple_html_dom();
      $this->dom->load($html);

      // process
      $this->process();
    }

    /**
     * getServerListUS
     * Gets the US server list
     *
     * @return  swtor_html_serverlist
     */
    public function getServerListUS()
    {
      return $this->swtor_servers_us;
    }

    /**
     * getServerListEU
     * Gets the EU server list
     *
     * @return  swtor_html_serverlist
     */
    public function getServerListEU()
    {
      return $this->swtor_servers_eu;
    }

    /**
     * getServerListAP
     * Gets the Asia/Pacific server list
     *
     * @return  swtor_html_serverlist
     */
    public function getServerListAP()
    {
      return $this->swtor_servers_ap;
    }

    /**
     * clear
     * Clear the memory of the dom object
     */
    public function clear()
    {
      $this->dom->clear();
    }

    /**
     * process
     * Process the DOM object and get the server lists
     */
    private function process()
    {
      // get the DOM list for the us servers
      $server_list_us = $this->dom->find("div[class=serverList]", 0);
      if ($server_list_us)
        $this->swtor_servers_us = new swtor_html_serverlist($server_list_us);

      // get the DOM list for the eu servers
      $server_list_eu = $this->dom->find("div[class=serverList]", 1);
      if ($server_list_eu)
        $this->swtor_servers_eu = new swtor_html_serverlist($server_list_eu);

      // get the DOM list for the ap servers
      $server_list_ap = $this->dom->find("div[class=serverList]", 2);
      if ($server_list_ap)
        $this->swtor_servers_ap = new swtor_html_serverlist($server_list_ap);
    }
  }
}

/*+----------------------------------------------------------------------------
  | swtor_html_serverlist
  +--------------------------------------------------------------------------*/
if (!class_exists('swtor_html_serverlist'))
{
  class swtor_html_serverlist
  {
    /* The DOM node for this server list*/
    private $dom;

    /**
     * Constructor
     *
     * @param  DOMDocument  $dom  The DOM node for this server list
     */
    public function __construct($dom)
    {
      $this->dom = $dom;
    }

    /**
     * getServers
     * Get a array of all available servers
     *
     * @return  array(swtor_html_server)
     */
    public function getServers()
    {
      $servers = array();

      // get an array of all DOM Nodes with <div class="serverBody" data-name="xxx">
      $serverNodes = $this->dom->find("div[data-name]");
      if (is_array($serverNodes))
      {
        foreach ($serverNodes as $serverNode)
          $servers[] = new swtor_html_server($serverNode);
      }

      return $servers;
    }
  }
}

/*+----------------------------------------------------------------------------
  | swtor_html_server
  +--------------------------------------------------------------------------*/
if (!class_exists('swtor_html_server'))
{
  class swtor_html_server
  {
    /* The DOM node for this server*/
    private $dom;

    /**
     * Constructor
     *
     * @param  DOMDocument  $dom  The DOM node for this server list
     */
    public function __construct($dom)
    {
      $this->dom = $dom;
    }

    /**
     * getter
     * Getter for all "properties"
     *
     * @param  string  $name  Name of the property to get
     *
     * @return  mixed
     */
    public function __get($name)
    {
      // data-status="UP" data-name="black vulkars" data-population="2" data-type="PVP" data-timezone="West"
      // data-status="UP" data-name="bao-dur"       data-population="1" data-type="PvE" data-language="English"

      switch ($name)
      {
      case 'status':
        return $this->dom->attr['data-status'];
      case 'name':
        $nameNode = $this->dom->find('.name', 0);
        return ($nameNode ? $nameNode->text() : 'Unknown');
      case 'population':
        return $this->dom->attr['data-population'];
      case 'type':
        return $this->dom->attr['data-type'];
      case 'timezone': // only available if region is us
        return (isset($this->dom->attr['data-timezone']) ? $this->dom->attr['data-timezone'] : 'Unknown');
      case 'language': // only available if region is eu
        return (isset($this->dom->attr['data-language']) ? $this->dom->attr['data-language'] : 'Unknown');
      case 'region':
        return (isset($this->dom->attr['data-timezone']) ? 'us' : 'eu');
      default:
        return null;
      }
    }
  }
}

?>
