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
 * $Id: tera_html.class.php 11807 2012-06-05 15:53:50Z shoorty $
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
  | tera_html
  +--------------------------------------------------------------------------*/
if (!class_exists('tera_html'))
{
  class tera_html
  {
    /* The server list */
    private $tera_servers = null;

    /* The DOM object */
    private $dom;

    /**
     * Constructor
     *
     * @param  string  $html  HTML data of the TERA status page
     */
    public function __construct($html)
    {
      $this->dom = new simple_html_dom();
      $this->dom->load($html);

      // process
      $this->process();
    }

    /**
     * getServerList
     * Gets the server list
     *
     * @return  tera_html_serverlist
     */
    public function getServerList()
    {
      return $this->tera_servers;
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
      // get the DOM list for the servers
      $server_list = $this->dom->find("div[id=serverstatus]", 0);
      if ($server_list)
        $this->tera_servers = new tera_html_serverlist($server_list);
    }
  }
}

/*+----------------------------------------------------------------------------
  | tera_html_serverlist
  +--------------------------------------------------------------------------*/
if (!class_exists('tera_html_serverlist'))
{
  class tera_html_serverlist
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

      // get an array of all DOM Nodes with <tr>
      $serverNodes = $this->dom->find("tr");
      if (is_array($serverNodes))
      {
        // skip first server cause this is the table heading
        $i = 0;
        foreach ($serverNodes as $serverNode)
        {
          if ($i++ != 0)
            $servers[] = new tera_html_server($serverNode);
        }
      }

      return $servers;
    }
  }
}

/*+----------------------------------------------------------------------------
  | tera_html_server
  +--------------------------------------------------------------------------*/
if (!class_exists('tera_html_server'))
{
  class tera_html_server
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
      // <td><img src="/design/community_site/img/server_on.png" alt="online" title="Online"/></td>
      // <td>Allemantheia</td>
      // <td class="PVE">PVE</td>
      // <td class="average">Medium</td>
      // <td class="lang">en</td>

      switch ($name)
      {
      case 'status':
        $node = $this->dom->find('td', 0);
        if (!$node) return 'unknown';
        $img_node = $node->find('img', 0);
        return ($img_node ? $img_node->attr['alt'] : 'unknown');
      case 'name':
        $node = $this->dom->find('td', 1);
        return ($node ? trim($node->text()) : 'Unknown');
      case 'population':
        $node = $this->dom->find('td', 3);
        return ($node ? $node->text() : 'unknown');
      case 'type':
        $node = $this->dom->find('td', 2);
        return ($node ? $node->text() : 'unknown');
      case 'language': // only available if region is eu
        $node = $this->dom->find('td', 4);
        return ($node ? $node->text() : 'unknown');
      default:
        return null;
      }
    }
  }
}

?>
