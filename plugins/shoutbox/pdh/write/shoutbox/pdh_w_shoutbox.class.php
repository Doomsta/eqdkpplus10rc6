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
 * $Id: pdh_w_shoutbox.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
  die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_w_shoutbox
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_w_shoutbox'))
{
  class pdh_w_shoutbox extends pdh_w_generic
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('db', 'pdh', 'time');
      return array_merge(parent::$shortcuts, $shortcuts);
    }

    /*
     * Number of chars to wrap after
     */
    private $wordwrap;

    /**
     * Constructor
     */
    public function __construct()
    {
      parent::__construct();

      // set default wordwrap
      $this->wordwrap = 20;
    }

    /**
     * add
     * Add a new shoutbox entry
     *
     * @param  int     $usermember_id  User or Member ID
     * @param  string  $text           Text to insert
     *
     * @returns mixed, on success shoutbox id, else false
     */
    public function add($usermember_id, $text)
    {
      // cleanup text
      $text = $this->cleanupText($text);

      // add to database
      $sql_data = array(
        'user_or_member_id' => $usermember_id,
        'shoutbox_date'     => $this->db->escape($this->time->time),
        'shoutbox_text'     => $this->db->escape($text)
      );
      $result = $this->db->query('INSERT INTO `__shoutbox` :params', $sql_data);
      if (!$result)
        return false;

      // do hooks
      $this->pdh->enqueue_hook('shoutbox_update');

      return $this->db->insert_id();
    }

    /**
     * delete
     * Delete a shoutbox entry from db
     *
     * @param  int   $shoutbox_id  Shoutbox ID
     *
     * @returns boolean
     */
    public function delete($shoutbox_id)
    {
      // delete from db
      $sql = 'DELETE FROM `__shoutbox` WHERE shoutbox_id='.$this->db->escape($shoutbox_id);
      $result = $this->db->query($sql);
      if (!$result)
        return false;

      // do hooks
      $this->pdh->enqueue_hook('shoutbox_update');

      return true;
    }

    /**
     * set_user
     * Updates an entry and sets member id to user id
     *
     * @param  int   $shoutbox_id  Shoutbox ID
     * @param  int   $user_id      User ID
     *
     * @returns boolean
     */
    public function set_user($shoutbox_id, $user_id)
    {
      // update in db
      $sql = 'UPDATE `__shoutbox`
              SET `user_or_member_id`='.$this->db->escape($user_id).'
              WHERE shoutbox_id='.$this->db->escape($shoutbox_id);
      $result = $this->db->query($sql);
      if (!$result)
        return false;

      // do hooks
      $this->pdh->enqueue_hook('shoutbox_update');

      return true;
    }

    /**
     * cleanupText
     * Cleans up the text to insert to database
     *
     * @param  string   $text  Text to insert
     *
     * @returns string
     */
    private function cleanupText($text)
    {
      // auto create url bbcode for URLs - by GodMod
	  $text = $this->autolink($text);
	  
	  // wrap words (do own handling cause of bbcodes)
      $cleanup_text = $this->shoutbox_wordwrap($text, $this->wordwrap, "\n", true);

      return trim($cleanup_text);
    }
	
	 /**
     * autolink
     * Converts an URL to appropriate BB-Code
     *
     * @param  string   $str Text to insert
     *
     * @returns string
     */
	private function autolink($str) {
		$str = ' ' . $str;
		$str = preg_replace(
		  '`([^"=\'>])(((http|https|ftp)://|www.)[^\s<]+[^\s<\.)])`i',
		  '$1[url="$2"]$2[/url]',
		  $str
		);
		$str = substr($str, 1);
		$str = preg_replace('`url=\"www`','url="http://www',$str);
		// fügt http:// hinzu, wenn nicht vorhanden
		return trim($str);
	}

    /**
     * shoutbox_wordwrap
     * Wrap words ignoring bb code
     *
     * @param   string   $text   Text to wrap
     * @param   integer  $width  Max length of one line
     * @param   string   $break  String to insert for line break, default '\n'
     * @param   boolean  $cut    cut inside of words?
     *
     * @return  string
     */
    private function shoutbox_wordwrap($text, $width, $break="\n", $cut=false)
    {
      // explode by spaces
      $element_array = explode(' ', $text);
      $count = count($element_array);

      // loop through all the elements
      $wraped_text = '';
      foreach($element_array as $org_text)
      {
        // explode by \n
        $inner_element_array = explode("\n", $org_text);
        foreach($inner_element_array as $inner_org_text)
        {
          // strip bbcode from text
          $striped_text = preg_replace('#\[[\w=]+\](.*?)\[/[\w]+\]#si', '\1', $inner_org_text);
          // get striped size
          $striped_size = strlen($striped_text);

          // do not wrap image/urls/emails
          $inner_cut = $cut;
          if (preg_match('#\[img\](.*?)\[/img\]#si', $inner_org_text) ||
              preg_match('#\[url=?"?(.*?)"?\](.*?)\[/url\]#si', $inner_org_text) ||
              preg_match('#\[email\](.*?)\[/email\]#si', $inner_org_text))
          {
            $inner_cut = false;
          }

          // fits?
          if ($striped_size > $width)
          {
            $new_text = wordwrap($striped_text, $width, $break, $inner_cut);
            // replace in original text
            $new_text = str_replace($striped_text, $new_text, $inner_org_text);
          }
          else
          {
            // fit, so just take original text
            $new_text = $inner_org_text;
          }

          // append to output
          $wraped_text .= $new_text."\n";
        }
        // replace last char by space
        $wraped_text[strlen($wraped_text)-1]= ' ';
      }

      return $wraped_text;
    }

  } //end class
} //end if class not exists

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_shoutbox', pdh_w_shoutbox::__shortcuts());
?>
