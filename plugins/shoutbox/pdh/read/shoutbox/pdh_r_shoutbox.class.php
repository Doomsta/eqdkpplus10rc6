<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-11-01 13:38:39 +0100 (Di, 01. Nov 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11419 $
 *
 * $Id: pdh_r_shoutbox.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
  die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_r_shoutbox
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_r_shoutbox'))
{
  class pdh_r_shoutbox extends pdh_r_generic
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('pdc', 'db', 'pdh', 'config', 'bbcode', 'time');
      return array_merge(parent::$shortcuts, $shortcuts);
    }

    /**
     * Data array loaded by initialize
     */
    private $data;

    /**
     * Path for smileys
     */
    private $smiley_path;

    /**
     * Hook array
     */
    public $hooks = array(
      'member_update',
      'user',
      'shoutbox_update'
    );

    /**
     * Presets array
     */
    public $presets = array(
      'sbdate' => array('date',           array('%shoutbox_id%', true),  array()), // true = Show Date
      'sbname' => array('usermembername', array('%shoutbox_id%'),        array()),
      'sbtext' => array('text',           array('%shoutbox_id%'),        array())
    );

    /**
     * Constructor
     */
    public function __construct()
    {
      $this->smiley_path = 'images/smilies';
    }

    /**
     * reset
     * Reset shoutbox read module by clearing cached data
     */
    public function reset()
    {
      $this->pdc->del('pdh_shoutbox_table');
      unset($this->data);
    }

    /**
     * init
     * Initialize the shoutbox read module by loading all information from db
     *
     * @returns boolean
     */
    public function init()
    {
      // try to get from cache first
      $this->data = $this->pdc->get('pdh_shoutbox_table');
      if($this->data !== NULL)
      {
        return true;
      }

      // empty array as default
      $this->data = array();

      // read all shoutbox entries from db
      $sql = 'SELECT
                shoutbox_id,
                user_or_member_id,
                shoutbox_date,
                shoutbox_text
              FROM `__shoutbox`
              ORDER BY shoutbox_date DESC;';
      $result = $this->db->query($sql);
      if ($result)
      {
        // get DST correction value
        $correction = date('I') * 3600;

        // add row by row to local copy
        while (($row = $this->db->fetch_record($result)))
        {
          $this->data[$row['shoutbox_id']] = array(
            'user_member_id' => $row['user_or_member_id'],
            'date'           => $row['shoutbox_date'],
            'text'           => $row['shoutbox_text']
          );
        }
        $this->db->free_result($result);
      }

      // add data to cache
      $this->pdc->put('pdh_shoutbox_table', $this->data, null);

      return true;
    }

    /**
     * get_id_list
     * Return the list of shoutbox ids
     *
     * @returns array(int)
     */
    public function get_id_list()
    {
      // empty id list as default
      $shoutbox_ids = array();

      // add each key of data as shoutbox id to id list
      if (is_array($this->data))
      {
        $keys = array_keys($this->data);
        foreach ($keys as $shoutbox_id)
        {
          $shoutbox_ids[] = $shoutbox_id;
        }
      }

      return $shoutbox_ids;
    }

    /**
     * get_userid
     * Return the user id corresponding to the shoutbox id
     *
     * @param  int  $shoutbox_id  shoutbox id
     *
     * @returns integer
     */
    public function get_userid($shoutbox_id)
    {
      // if we use users, just return the "memberuserid"; otherwise get user id from member id
      if ($this->config->get('sb_use_users', 'shoutbox'))
      {
        return $this->get_usermemberid($shoutbox_id);
      }
      else
      {
        return $this->pdh->get('member', 'userid', array($this->get_usermemberid($shoutbox_id)));
      }
    }

    /**
     * get_memberid
     * Return the member id corresponding to the shoutbox id
     *
     * @param  int  $shoutbox_id  shoutbox id
     *
     * @returns integer
     */
    public function get_memberid($shoutbox_id)
    {
      // if we use users, return -1, cause no member id is assigned; otherwise just return the "memberuserid"
      if ($this->config->get('sb_use_users', 'shoutbox'))
      {
        return -1;
      }
      else
      {
        return $this->get_usermemberid($shoutbox_id);
      }
    }

    /**
     * get_usermemberid
     * Return the user or member id corresponding to the shoutbox id
     *
     * @param  int  $shoutbox_id  shoutbox id
     *
     * @returns integer
     */
    public function get_usermemberid($shoutbox_id)
    {
      if (is_array($this->data[$shoutbox_id]) && isset($this->data[$shoutbox_id]['user_member_id']))
      {
        return $this->data[$shoutbox_id]['user_member_id'];
      }

      return -1;
    }

    /**
     * get_usermembername
     * Return the user or member name corresponding to the shoutbox id
     *
     * @param  int  $shoutbox_id  Shoutbox ID
     *
     * @returns string
     */
    public function get_usermembername($shoutbox_id)
    {
      if ($this->config->get('sb_use_users', 'shoutbox'))
      {
        return $this->pdh->get('user', 'name', array($this->get_usermemberid($shoutbox_id)));
      }
      else
      {
        return $this->pdh->get('member', 'name', array($this->get_usermemberid($shoutbox_id), false, false));
      }
    }

    /**
     * get_html_usermembername
     * Return the user or member name corresponding to the shoutbox id as html
     *
     * @param  int  $shoutbox_id  Shoutbox ID
     *
     * @returns string
     */
    public function get_html_usermembername($shoutbox_id)
    {
      if ($this->config->get('sb_use_users', 'shoutbox'))
      {
        return $this->pdh->geth('user', 'name', array($this->get_usermemberid($shoutbox_id)));
      }
      else
      {
        return $this->pdh->geth('member', 'name', array($this->get_usermemberid($shoutbox_id), false, false));
      }
    }

    /**
     * get_text
     * Return the text corresponding to the shoutbox id
     *
     * @param  int  $shoutbox_id  shoutbox id
     *
     * @returns string
     */
    public function get_text($shoutbox_id)
    {
      if (is_array($this->data[$shoutbox_id]) && isset($this->data[$shoutbox_id]['text']))
      {
        // within the db a \n is stored as literal "\n", so we have to redo this by replacing
        $text = str_replace('\n', "\n", $this->data[$shoutbox_id]['text']);
        return stripslashes($text);
      }

      return '';
    }

    /**
     * get_html_text
     * Return the text corresponding to the shoutbox id as html
     *
     * @param  int      $shoutbox_id  shoutbox id
     * @param  string   $rpath        root path
     *
     * @returns string
     */
    public function get_html_text($shoutbox_id, $rpath='')
    {
      // root path
      $root_path = ($rpath != '') ? $rpath : $this->root_path;
      // smilie path
      $smilie_path = $root_path.$this->smiley_path;

      // get text
      $text = $this->get_text($shoutbox_id);

      // wrap around with <p>
      $text = '<p>'.trim($text).'</p>';

      // bbcodes
      $this->bbcode->SetSmiliePath($smilie_path);
      $text = $this->bbcode->toHTML($text, true);
      $text = $this->bbcode->MyEmoticons($text);

      // for some unknown reasons, after the BBCode actions, we get some \n, but <br/> are already inserted.
      // so just remove the \n's from the text
      $text = str_replace("\n", '', $text);

      return $text;
    }

    /**
     * get_date
     * Return the timestamp corresponding to the shoutbox id
     *
     * @param  int  $shoutbox_id  shoutbox id
     *
     * @returns integer
     */
    public function get_date($shoutbox_id)
    {
      if (is_array($this->data[$shoutbox_id]) && isset($this->data[$shoutbox_id]['date']))
      {
        return $this->data[$shoutbox_id]['date'];
      }

      return 0;
    }

    /**
     * get_date
     * Return the timestamp corresponding to the shoutbox id as html
     *
     * @param  int      $shoutbox_id   Shoutbox ID
     * @param  boolean  $show_date     Show date also or just time
     *
     * @returns string
     */
    public function get_html_date($shoutbox_id, $show_date=false)
    {
      if ($show_date)
        $date = $this->time->user_date($this->get_date($shoutbox_id), true, false, false);
      else
        $date = $this->time->user_date($this->get_date($shoutbox_id), false, true, false);

      return $date;
    }

    /**
     * get_count
     * Return the number of shoutbox entries
     *
     * @returns integer
     */
    public function get_count()
    {
      if ($this->data && is_array($this->data))
      {
        return count($this->data);
      }

      return 0;
    }

    /**
     * get_search
     * Searches the shoutbox module for the search value
     *
     * @param  string  $search  Value to search
     *
     * @returns array
     */
    public function get_search($search)
    {
      // empty search results
      $searchResults = array();

      // loop through the data array and fill search results
      if ($this->data && is_array($this->data))
      {
        foreach ($this->data as $shoutbox_id => $data)
        {
          $member = $this->get_usermembername($shoutbox_id);
          $text   = $this->get_text($shoutbox_id);

          if (strpos($text, $search) !== false || strpos($member, $search) !== false)
          {
            $searchResults[] = array(
              'id'   => $this->get_html_date($shoutbox_id, true).'<br/>'.$this->get_html_usermembername($shoutbox_id),
              'name' => $this->get_html_text($shoutbox_id),
              'link' => $this->root_path.'plugins/shoutbox/archive.php'.$this->SID.'&amp;id='.$shoutbox_id,
            );
          }
        }
      }

      return $searchResults;
    }

  } //end class
} //end if class not exists

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_shoutbox', pdh_r_shoutbox::__shortcuts());
?>
