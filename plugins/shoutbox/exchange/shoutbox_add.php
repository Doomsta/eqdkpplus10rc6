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
 * $Id: shoutbox_add.php 11795 2012-05-30 21:41:49Z wallenium $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | exchange_shoutbox_add
  +--------------------------------------------------------------------------*/
if (!class_exists('exchange_shoutbox_add'))
{
  class exchange_shoutbox_add extends gen_class
  {
    /* List of dependencies */
    public static $shortcuts = array('user', 'config', 'pdh', 'pex'=>'plus_exchange');

    /**
     * Additional options
     */
    public $options = array();

    /**
     * post_shoutbox_add
     * POST Request to add shoutbox entry
     *
     * @param   array   $params   Parameters array
     * @param   string  $body     XML body of request
     *
     * @returns array
     */
    function post_shoutbox_add($params, $body)
    {
      // be sure user is logged in
      if ($this->user->is_signedin())
      {
        // parse xml request
        $xml = simplexml_load_string($body);
        $member_id = ($xml && $xml->charid) ? intval($xml->charid) : intval($this->pdh->get('user', 'mainchar', array($this->user->data['user_id'])));
        $text      = ($xml && $xml->text)   ? trim($xml->text)     : '';

        // check if member id is valid for this user
        $valid_members = $this->pdh->get('member', 'connection_id', array($this->user->data['user_id']));
        $member_valid = (is_array($valid_members) && in_array($member_id, $valid_members)) ? true : false;

        // if we are in "user" mode OR member is valid, continue
        if ($this->config->get('sb_use_users', 'shoutbox') || $member_valid)
        {
          // get usermember_id
          $usermember_id = ($this->config->get('sb_use_users', 'shoutbox') ? intval($this->user->data['user_id']) : $member_id);

          if (!empty($text) && $usermember_id > 0)
          {
            // insert xml text
            include_once($this->root_path.'plugins/shoutbox/includes/common.php');
            $result = register('ShoutboxClass')->insertShoutboxEntry($usermember_id, trim($text));

            // return status
            $response = array('status' => ($result) ? 1 : 0);
          }
          else
          {
            // missing data
            if (empty($text))
              $response = $this->pex->error($this->user->lang('sb_missing_text'));
            else
              $response = $this->pex->error($this->user->lang('sb_missing_char_id'));
          }
        }
        else
        {
          $response = $this->pex->error($this->user->lang('sb_missing_char_id'));
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

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_shoutbox_add', exchange_shoutbox_add::$shortcuts);
?>
