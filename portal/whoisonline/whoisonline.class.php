<?php
 /*
 * Project:   EQdkp-Plus
 * License:   Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:      http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:     2008
 * Date:      $Date: 2012-05-30 23:42:47 +0200 (Mi, 30. Mai 2012) $
 * -----------------------------------------------------------------------
 * @author    $Author: wallenium $
 * @copyright 2008-2011 Aderyn
 * @link      http://eqdkp-plus.com
 * @package   eqdkp-plus
 * @version   $Rev: 11796 $
 *
 * $Id: whoisonline.class.php 11796 2012-05-30 21:42:47Z wallenium $
 */

if (!defined('EQDKP_INC')){
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | mmo_whoisonline
  +--------------------------------------------------------------------------*/
if (!class_exists('mmo_whoisonline'))
{
  class mmo_whoisonline extends gen_class
  {
    /* list of dependencies */
    public static $shortcuts = array('core', 'db', 'pdc', 'user', 'time', 'html', 'config');

    /* Array of online users */
    private $online_users = array();

    /* Array of offline users */
    private $offline_users = array();

    /* Limit of users to display */
    private $limit;

    /* Cache data 5 minutes */
    private $cachetime = 300;

    /* image path */
    private $image_path;

    /* is admin to manage? */
    private $is_admin;


    /**
     * Constructor
     *
     * @param  $wherevalue  string  position of portal module
     */
    public function __construct($wherevalue){

      // limit of users
      $this->limit = ($this->config->get('wo_limit') && $this->config->get('wo_limit') != '') ? $this->config->get('wo_limit') : 10;

      // get image path
      $this->image_path = $this->root_path.'images/glyphs';

      // get is admin
      $this->is_admin = ($this->user->is_signedin() && $this->user->check_auth('a_users_man', false)) ? true : false;

      // load online users
      $this->loadOnlineUsers();
      // load offline users if enabled
      if (!$this->config->get('wo_dontshowoffline'))
        $this->loadOfflineUsers();
    }

    /**
     * getPortalOutput
     * get Portal output
     *
     * @return string
     */
    public function getPortalOutput(){

      // get number of online and offline users to display
      $online_user_count = count($this->online_users);
      $offline_user_count = min($this->limit - $online_user_count, count($this->offline_users));

      // table header
      $output = '<div class="table colorswitch hoverrows">';

      // output online users
      foreach ($this->online_users as $user_row){
        // show as online
        $output .= '<div class="tr">
                      <div class="td center"><img src="'.$this->image_path.'/status_green.gif" alt="online" /></div>
                      <div class="td">'.$this->html->ToolTip($this->user->lang('wo_online'), $this->getUsername($user_row)).'</div>
                    </div>';
      }

      // output offline users
      if ($offline_user_count > 0 && !$this->config->get('wo_dontshowoffline'))
      {
        $index = 0;
        foreach ($this->offline_users as $user_id => $user_row)
        {
          // end loop if max offline users are reached
          if ($index++ >= $offline_user_count)
            break;

          // show as offline
          $output .= '<div class="tr">
                        <div class="td center"><img src="'.$this->image_path.'/status_red.gif" alt="offline"/></div>
                        <div class="td">'.$this->html->ToolTip($this->user->lang('wo_last_online').'<br/>'.$this->time->date($this->user->lang('wo_date_format'), $user_row['lastvisit']), $this->getUsername($user_row)).'</div>
                      </div>';
        }
      }

      // table end
      $output .= '</div>';

      return $output;
    }

    /**
     * getUsername
     * get username out of user data and make admin link if we have rights to do so
     *
     * @param  $user_row  array  user array
     *
     * @return string
     */
    private function getUsername($user_row){
      if (!is_array($user_row))
        return '';

      if ($this->is_admin)
        return '<a href="'.$this->root_path.'admin/manage_users.php'.$this->SID.'&amp;u='.$user_row['user_id'].'">'.$user_row['username'].'</a>';
      else
        return '<a href="'.$this->root_path.'listusers.php'.$this->SID.'&amp;u='.$user_row['user_id'].'">'.$user_row['username'].'</a>';
    }

    /**
     * loadOnlineUsers
     * get list of online users
     */
    private function loadOnlineUsers(){

      // try to get online users from cache
      $this->online_users = $this->pdc->get('portal.module.whoisonline.online', false, true);
      if (!$this->online_users)
      {
        // empty array
        $this->online_users = array();

        // get all online users
        $sql = 'SELECT u.user_id, u.username, u.user_lastvisit
                FROM __sessions s
                LEFT JOIN __users u
                ON u.user_id = s.session_user_id
                WHERE u.user_active = \'1\'
                GROUP BY u.username
                ORDER BY u.user_lastvisit DESC
                LIMIT 0,'.$this->limit;
        $result = $this->db->query($sql);
        if ($result)
        {
          // fetch users
          while ($row = $this->db->fetch_record($result))
          {
            // for some unknown reason, there is sometimes an empty user id
            if ($row['user_id'])
            {
              $this->online_users[$row['user_id']] = array(
                  'user_id'   => $row['user_id'],
                  'username'  => $row['username'],
                  'lastvisit' => $row['user_lastvisit']
              );
            }
          }
          $this->db->free_result($result);

          // cache result
          $this->pdc->put('portal.module.whoisonline.online', $this->online_users, $this->cachetime, false, true);
        }
      }
    }

    /**
     * loadOfflineUsers
     * get list of offline users
     */
    private function loadOfflineUsers(){

      // try to get online users from cache
      $this->offline_users = $this->pdc->get('portal.module.whoisonline.offline', false, true);
      if ($this->offline_users === null){
        // reset array
        $this->offline_users = array();

        // get last active users (2x limit for ensuring enough users)
        $sql = 'SELECT user_id, username, user_lastvisit
                FROM __users
                WHERE user_active = \'1\'
                GROUP BY username
                ORDER BY user_lastvisit DESC
                LIMIT 0,'.(2 * $this->limit);
        $result = $this->db->query($sql);
        if ($result)
        {
          // fetch users
          while ($row = $this->db->fetch_record($result))
          {
            $this->offline_users[$row['user_id']] = array(
                'user_id'   => $row['user_id'],
                'username'  => $row['username'],
                'lastvisit' => $row['user_lastvisit']
            );
          }
          $this->db->free_result($result);

          // build difference from online to offline users
          if (is_array($this->online_users))
            $this->offline_users = array_diff_key($this->offline_users, $this->online_users);

          // cache result
          $this->pdc->put('portal.module.whoisonline.offline', $this->offline_users, $this->cachetime, false, true);
        }
      }
    }
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_mmo_whoisonline', mmo_whoisonline::$shortcuts);
?>
