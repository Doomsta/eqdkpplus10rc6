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
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11830 $
 *
 * $Id: shoutbox.php 11830 2012-06-22 18:20:07Z godmod $
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once('./includes/common.php');


// Be sure plugin is installed
if (registry::register('plugin_manager')->check('shoutbox', PLUGIN_INSTALLED))
{
  $in = registry::register('input');

  // get post/get values
  $sb_text          = $in->get('sb_text');
  $sb_usermember_id = $in->get('sb_usermember_id', -1);
  $sb_delete        = $in->get('sb_delete', 0);
  $sb_root          = $in->get('sb_root');
  $sb_orientation   = $in->get('sb_orientation');

  // -- Insert? ---------------------------------------------
  if ($sb_text && $sb_member_id != -1)
  {
    $shoutbox->insertShoutboxEntry($sb_usermember_id, $sb_text);
  }
  // -- Delete? ---------------------------------------------
  else if ($sb_delete)
  {
    $shoutbox->deleteShoutboxEntry($sb_delete);
  }

  // -- Output ----------------------------------------------
  echo $shoutbox->getContent($sb_orientation, urldecode($sb_root), true);
}
else
{
  $error = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline colorswitch">
              <tr>
                <td><div class="center">'.registry::fetch('user')->lang('sb_plugin_not_installed').'</div></td>
              </tr>
            </table>';
  echo $error;
}

?>
