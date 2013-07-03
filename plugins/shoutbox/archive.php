<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-10-13 22:48:23 +0200 (Sa, 13. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 12273 $
 *
 * $Id: archive.php 12273 2012-10-13 20:48:23Z godmod $
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('PLUGIN', 'shoutbox');

$eqdkp_root_path = './../../';
include_once('includes/common.php');


/*+----------------------------------------------------------------------------
  | ShoutboxArchive
  +--------------------------------------------------------------------------*/
class ShoutboxArchive extends page_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('pm', 'user', 'core', 'in', 'pdh', 'time', 'tpl');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('shoutbox', PLUGIN_INSTALLED))
      message_die($this->user->lang('sb_plugin_not_installed'));

    $handler = array(
      'sb_delete' => array('process' => 'delete', 'csrf' => true, 'check' => 'a_shoutbox_delete'),
    );
    parent::__construct('u_shoutbox_view', $handler);

    $this->process();
  }

  /**
   * delete
   * Delete entries
   */
  public function delete()
  {
    $messages = array();
    $rc = register('ShoutboxClass')->deleteShoutboxEntry($this->in->get('delete_id', 0));
    if ($rc !== false)
      $messages[] = $this->user->lang('sb_delete_success');

    $this->display($messages);
  }

  /**
   * display
   * Display the page
   *
   * @param  array   $messages  Array of Messages to output
   */
  public function display($messages=array())
  {
    // -- Messages ------------------------------------------------------------
    if ($messages)
    {
      foreach($messages as $name)
        $this->core->message($name, $this->user->lang('shoutbox'), 'green');
    }

    // -- get all shoutbox id's -----------------------------------------------
    $shoutbox_ids = $this->pdh->get('shoutbox', 'id_list', array());
    $shoutbox_out = array();


    // -- build 2D array with [year][month] -----------------------------------
    $date_array = array();
    foreach ($shoutbox_ids as $shoutbox_id)
    {
      $shoutbox_date       = $this->pdh->get('shoutbox', 'date', array($shoutbox_id));
      $shoutbox_date_year  = $this->time->date('Y', $shoutbox_date);
      $shoutbox_date_month = $this->time->date('m', $shoutbox_date);
      $date_array[$shoutbox_date_year][$shoutbox_date_month][] = $shoutbox_id;
    }


    // -- output date select on left side -------------------------------------
    foreach ($date_array as $year => $months)
    {
      $this->tpl->assign_block_vars('year_row', array(
        'YEAR' => $year
      ));

      foreach ($months as $month => $ids)
      {
        $this->tpl->assign_block_vars('year_row.month_row', array(
          'MONTH'     => $this->time->date('F', $this->time->mktime(0, 0, 0, $month, 1, $year)),
          'COUNT'     => count($ids),
          'LINK_VIEW' => $this->root_path.'plugins/shoutbox/archive.php'.$this->SID.'&amp;year='.$year.'&amp;month='.$month
        ));
      }
    }


    // -- year/month select? --------------------------------------------------
    $page_title = '';
    if ($this->in->exists('year') && $this->in->exists('month'))
    {
      // add all shoutbox entries within date/month to the output array
      $shoutbox_out = $date_array[$this->in->get('year')][$this->in->get('month')];
      $page_title   = $this->time->date('F', $this->time->mktime(0, 0, 0, $this->in->get('month'), 1, $this->in->get('year'))).' '.$this->in->get('year');
    }
    // -- search? -------------------------------------------------------------
    else if ($this->in->exists('search') && strlen($this->in->exists('search')))
    {
      // loop through all the shoutbox entries and try to find in either username or in text
      foreach ($shoutbox_ids as $shoutbox_id)
      {
        $text   = $this->pdh->get('shoutbox', 'text',           array($shoutbox_id));
        $member = $this->pdh->get('shoutbox', 'usermembername', array($shoutbox_id));
        $search = $this->in->get('search');
        if (strpos($text, $search) !== false || strpos($member, $search) !== false)
          $shoutbox_out[] = $shoutbox_id;
        $page_title = $this->user->lang('search').': '.sanitize($this->in->get('search'));
      }
    }
    // -- id? -----------------------------------------------------------------
    else if ($this->in->exists('id'))
    {
      $shoutbox_out[] = $this->in->get('id');
    }
    // -- last month ----------------------------------------------------------
    else if (count($shoutbox_ids) > 0)
    {
      // show the last month only
      $shoutbox_date       = $this->pdh->get('shoutbox', 'date', array($shoutbox_ids[0]));
      $shoutbox_date_year  = $this->time->date('Y', $shoutbox_date);
      $shoutbox_date_month = $this->time->date('m', $shoutbox_date);
      $shoutbox_out = $date_array[$shoutbox_date_year][$shoutbox_date_month];
      $page_title   = $this->time->date('F', $this->time->mktime(0, 0, 0, $shoutbox_date_month, 1, $shoutbox_date_year)).' '.$shoutbox_date_year;
    }


    // -- output filtered data ------------------------------------------------
    foreach ($shoutbox_out as $shoutbox_id)
    {
      // show a new date row if it's not the same as the last one
      $shoutbox_date = $this->pdh->get('shoutbox', 'date', array($shoutbox_id));

      // output
      $this->tpl->assign_block_vars('shoutbox_row', array(
        'ID'      => $shoutbox_id,
        'NAME'    => $this->pdh->geth('shoutbox', 'usermembername', array($shoutbox_id)),
        'DATE'    => $this->time->date($this->user->style['date'], $shoutbox_date),
        'TIME'    => $this->time->date($this->user->style['time'], $shoutbox_date),
        'MESSAGE' => $this->pdh->geth('shoutbox', 'text', array($shoutbox_id))
      ));
    }
    // -- Template ----------------------------------------------------------------
    $this->tpl->assign_vars(array(
      // Form
      'S_YEAR'            => $this->in->get('year', ''),
      'S_MONTH'           => $this->in->get('month', ''),
      'S_SB_SEARCH'       => $this->in->get('search', ''),
      'S_COUNT'           => count($shoutbox_out),
      'S_PAGE_TITLE'      => ($page_title != '') ? '&raquo; '.$page_title : '',

      // Admin
      'CAN_DELETE'        => $this->user->check_auth('a_shoutbox_delete', false),
    ));


    // -- EQDKP ---------------------------------------------------------------
    $this->core->set_vars(array (
      'page_title'    => $this->user->lang('sb_shoutbox_archive').' '.$page_title,
      'template_path' => $this->pm->get_data('shoutbox', 'template_path'),
      'template_file' => 'archive.html',
      'display'       => true
    ));
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ShoutboxArchive', ShoutboxArchive::__shortcuts());
register('ShoutboxArchive');

?>
