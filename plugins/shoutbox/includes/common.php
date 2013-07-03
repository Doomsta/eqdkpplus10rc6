<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-09-02 10:09:49 +0200 (Fr, 02. Sep 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: Aderyn $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11183 $
 *
 * $Id: common.php 11183 2011-09-02 08:09:49Z Aderyn $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}

// -- Pluskernel common.php ---------------------------------------------------
if (!isset($eqdkp_root_path))
{
  $eqdkp_root_path = './';
}
include_once($eqdkp_root_path.'common.php');


// -- Used Classes ------------------------------------------------------------
include_once(registry::get_const('root_path').'plugins/shoutbox/includes/shoutbox.class.php');
$shoutbox = registry::register('ShoutboxClass');


// -- Check requirements ------------------------------------------------------
if (is_object($shoutbox))
{
  $sb_req_check = $shoutbox->checkRequirements();
  if ($sb_req_check !== true)
  {
    message_die($sb_req_check);
  }
}

?>
