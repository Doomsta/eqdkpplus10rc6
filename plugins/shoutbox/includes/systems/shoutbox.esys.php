<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-06-22 20:20:07 +0200 (Fr, 22. Jun 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2009-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11830 $
 *
 * $Id: shoutbox.esys.php 11830 2012-06-22 18:20:07Z godmod $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}

$systems_shoutbox = array(
  'pages' => array(
    'manage' => array(
      'name' => 'hptt_shoutbox_manage',
      'table_main_sub' => '%shoutbox_id%',
      'table_sort_dir' => 'desc',
      'page_ref' => 'manage.php',
      'show_select_boxes' => registry::fetch('user')->check_auth('a_shoutbox_delete', false),
      'table_presets' => array(
        array('name' => 'sbdate', 'sort' => true,  'th_add' => 'align="center" width="120px"', 'td_add' => 'align="center" nowrap="nowrap"'),
        array('name' => 'sbname', 'sort' => true,  'th_add' => 'align="center" width="20%"',   'td_add' => 'nowrap="nowrap"'),
        array('name' => 'sbtext', 'sort' => false, 'th_add' => 'align="center"',               'td_add' => '')
      ),
      'super_row' => array(
        array('colspan' => '1', 'align' => 'center', 'text' => '<input type="checkbox" name="sb_delete_all" id="sb_delete_all"/>'),
        array('colspan' => '3', 'align' => 'left',   'text' => '')
      )
    ),
  )
);

?>
