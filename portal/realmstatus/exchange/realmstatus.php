<?php
 /*
 * Project:   EQdkp-Plus
 * License:   Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:   2008
 * Date:    $Date: 2012-02-20 08:24:04 +0100 (Mo, 20. Feb 2012) $
 * -----------------------------------------------------------------------
 * @author    $Author: Aderyn $
 * @copyright 2008-2011 Aderyn
 * @link    http://eqdkp-plus.com
 * @package   eqdkp-plus
 * @version   $Rev: 11695 $
 *
 * $Id: realmstatus.php 11695 2012-02-20 07:24:04Z Aderyn $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | exchange_realmstatus
  +--------------------------------------------------------------------------*/
if (!class_exists('exchange_realmstatus'))
{
  class exchange_realmstatus extends gen_class
  {
    /* List of dependencies */
    public static $shortcuts = array('user', 'game', 'pex' => 'plus_exchange');
    
    /* Additional options */
    public $options = array();
    
    /**
     * get_realmstatus
     * GET Request for realmstatus entries
     *
     * @param   array   $params   Parameters array
     * @param   string  $body     XML body of request
     *
     * @returns array
     */
    public function get_realmstatus($params, $body)
    {
      // set default response
      $response = array('realms' => array());
      
      // try to load the status file for this game
      $game_name = strtolower($this->game->get_game());
      $status_file = $this->root_path.'portal/realmstatus/'.$game_name.'/status.class.php';
      if (file_exists($status_file))
      {
        include_once($status_file);

        $class_name = $game_name.'_realmstatus';
        $status = registry::register($class_name);
        if ($status)
          $response['realms'] = $status->getExchangeOutput();
        else
          return $this->pex->error($this->user->lang('rs_game_not_supported'));
      }
      else
      {
        return $this->pex->error($this->user->lang('rs_game_not_supported'));
      }
      
      return $response;
    }
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_realmstatus', exchange_realmstatus::$shortcuts);
?>
