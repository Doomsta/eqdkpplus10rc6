<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-05-30 23:41:49 +0200 (Mi, 30. Mai 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11795 $
 *
 * $Id: shoutbox_list.php 11795 2012-05-30 21:41:49Z wallenium $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | exchange_shoutbox_list
  +--------------------------------------------------------------------------*/
if (!class_exists('exchange_shoutbox_list'))
{
  class exchange_shoutbox_list extends gen_class
  {
    /* List of dependencies */
    public static $shortcuts = array('user', 'pdh', 'env', 'time', 'pex'=>'plus_exchange');

    /**
     * Additional options
     */
    public $options = array();

    /**
     * get_shoutbox_list
     * GET Request for shoutbox entries
     *
     * @param   array   $params   Parameters array
     * @param   string  $body     XML body of request
     *
     * @returns array
     */
    public function get_shoutbox_list($params, $body)
    {
      // set response
      $response = array('entries' => array());

      // be sure user is logged in
      if ($this->user->is_signedin())
      {
        // get the number of shoutbox entries to return
        $max_count = (isset($params['get']['number']) && intval($params['get']['number']) > 0) ? intval($params['get']['number']) : 10;
        // get sort direction
        $sort = (isset($params['get']['sort']) && $params['get']['sort'] == 'desc') ? 'desc' : 'asc';

        // get all shoutbox id's
        $shoutbox_ids = $this->pdh->get('shoutbox', 'id_list');
        if (is_array($shoutbox_ids))
        {
          // slice array
          $shoutbox_ids = array_slice($shoutbox_ids, 0, $max_count);

          // sort sliced array
          $shoutbox_ids = $this->pdh->sort($shoutbox_ids, 'shoutbox', 'date', $sort);

          // set root path
          $root = $this->env->httpHost.$this->env->server_path;

          // build entry array
          foreach ($shoutbox_ids as $shoutbox_id)
          {
            $response['entries']['entry:'.$shoutbox_id] = array(
              'id'        => $shoutbox_id,
              'member_id' => $this->pdh->get('shoutbox', 'memberid', array($shoutbox_id)),
              'user_id'   => $this->pdh->get('shoutbox', 'userid', array($shoutbox_id)),
              'name'      => $this->pdh->get('shoutbox', 'usermembername', array($shoutbox_id)),
              'text'      => $this->pdh->geth('shoutbox', 'text', array($shoutbox_id, $root)),
              'date'      => $this->time->date('Y-m-d H:i', $this->pdh->get('shoutbox', 'date', array($shoutbox_id))),
              'timestamp' => $this->pdh->get('shoutbox', 'date', array($shoutbox_id)),
            );
          }
        }
      }
      else
      {
        $response = $this->pex->error('access denied');
      }

      return $response;
    }

  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_shoutbox_list', exchange_shoutbox_list::$shortcuts);
?>
