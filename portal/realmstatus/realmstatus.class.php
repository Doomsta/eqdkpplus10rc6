<?php
 /*
 * Project:   EQdkp-Plus
 * License:   Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:   2008
 * Date:    $Date: 2012-10-07 09:08:20 +0200 (So, 07. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author    $Author: godmod $
 * @copyright 2008-2011 Aderyn
 * @link    http://eqdkp-plus.com
 * @package   eqdkp-plus
 * @version   $Rev: 12211 $
 *
 * $Id: realmstatus.class.php 12211 2012-10-07 07:08:20Z godmod $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

/*+----------------------------------------------------------------------------
  | mmo_realmstatus
  +--------------------------------------------------------------------------*/
if (!class_exists('mmo_realmstatus'))
{
  abstract class mmo_realmstatus extends gen_class
  {
    /* List of dependencies */
    public static $shortcuts = array('core', 'user', 'config', 'pex' => 'plus_exchange', 'tpl');

    /* Game name */
    protected $game_name = 'unknown';

    /* List of servers to process */
    private $servers = array();

    /**
     * Constructor
     */
    public function __construct()
    {
      // load server list
      $this->loadServerList();
    }

    /**
     * checkServer
     * Check if specified server is up/down/unknown
     *
     * @param  string  $servername  Name of server to check
     *
     * @return string ('up', 'down', 'unknown')
     */
    public abstract function checkServer($servername);

    /**
     * getPortalOutput
     * get Portal output
     *
     * @return string
     */
    public function getPortalOutput()
    {
      $output = '';

      // no realm specified?
      if (count($this->servers) > 0)
      {
        // output any CSS
        $this->outputCSS();

        // output JS code
        $this->outputJSCode();

        $output = '<div id="realmstatus_output_'.$this->game_name.'">
                     <div class="center" style="margin: 4px;">
                       <img src="'.$this->root_path.'images/global/loading.gif" alt="" />&nbsp;'.$this->user->lang('rs_loading').'
                     </div>
                   </div>';
      }
      else
      {
        $output = '<div class="center">'.$this->user->lang('rs_no_realmname').'</div>';
      }

      return $output;
    }

    /*
     * getExchangeOutput
     * get Exchange output
     *
     * @return array
     */
    public function getExchangeOutput()
    {
      $output = array();

      // no realm specified?
      if (count($this->servers) > 0)
      {
        foreach ($this->servers as $server)
        {
          $output[] = array(
            'name'   => trim($server),
            'status' => $this->checkServer(trim($server)),
          );
        }
      }
      else
      {
        return $this->pex->error($this->user->lang('rs_no_realmname'));
      }

      return $output;
    }

    /**
     * getJQueryOutput
     * get async JQuery output
     *
     * @return string
     */
    public function getJQueryOutput()
    {
      $output = '';

      // no realm specified?
      if (count($this->servers) > 0)
      {
        // wrap within table
        $output .= '<div class="table">';
        $output .= $this->getOutput($this->servers);
        $output .= '</div>';
      }
      else
      {
        $output .= '<div class="center">'.$this->user->lang('rs_no_realmname').'</div>';
      }

      return $output;
    }

    /**
     * getOutput
     * Get the portal output for all servers
     *
     * @param  array  $servers  Array of server names
     *
     * @return string
     */
    protected abstract function getOutput($servers);

    /**
     * outputCSS
     * Output CSS
     */
    protected abstract function outputCSS();

    /**
     * loadServerList
     * get list of servers to process
     */
    private function loadServerList()
    {
      // set empty list of realms
      $this->servers = array();

      // list of realms by portal modul config?
      if ($this->config->get('rs_realm') && strlen($this->config->get('rs_realm')) > 0)
      {
        // build array by exploding
        $this->servers = explode(',', $this->config->get('rs_realm'));
      }
      else if ($this->config->get('uc_servername') && strlen($this->config->get('uc_servername')) > 0)
      {
        // realm name by plus config?
        $this->servers[] = $this->config->get('uc_servername');
      }
    }

    /**
     * outputJSCode
     * output the javascript code for async portal output
     */
    private function outputJSCode()
    {
      // build JS for Async load
      $jscode = '$.ajax({
                    url: "'.$this->root_path.'portal/realmstatus/realmstatus.php'.$this->SID.'",
                    data: {
                      game: "'.$this->game_name.'"
                    },
                    success: function(data, textStatus, jqXHR) {
                      $(\'#realmstatus_output_'.$this->game_name.'\').html(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                      var htmlOut = \'<div class="center" style="margin:2px;">'.$this->user->lang('rs_loading_error').'</div>\';
                      $(\'#realmstatus_output_'.$this->game_name.'\').html(htmlOut);
                    },
                    dataType: "html"
                 });';

      $this->tpl->add_js($jscode, 'docready');
    }
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_mmo_realmstatus', mmo_realmstatus::$shortcuts);
?>
