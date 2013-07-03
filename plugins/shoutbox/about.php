<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-09-02 14:27:13 +0200 (Fr, 02. Sep 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: Aderyn $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11189 $
 *
 * $Id: about.php 11189 2011-09-02 12:27:13Z Aderyn $
 */

define('EQDKP_INC', true);
define('PLUGIN', 'shoutbox');

$eqdkp_root_path = './../../';
include_once($eqdkp_root_path.'common.php');


// -- Plugin installed? -------------------------------------------------------
$pm = register('plugin_manager');
if (!$pm->check('shoutbox', PLUGIN_INSTALLED) )
{
  message_die('Shoutbox plugin not installed.');
}


// ----------------------------------------------------------------------------
register('template')->assign_vars(array(
    'L_VERSION' => $pm->get_data('shoutbox', 'version'),
    'L_BUILD'   => $pm->plugins['shoutbox']->build,
    'L_STATUS'  => $pm->plugins['shoutbox']->vstatus,
    'L_YEARR'   => register('timehandler')->date('Y'),
));


// ----------------------------------------------------------------------------
register('core')->set_vars(array(
  'page_title'    => 'About Shoutbox',
  'template_file' => 'about.html',
  'template_path' => $pm->get_data('shoutbox', 'template_path'),
  'header_format' => 'none',
  'display'       => true)
);

?>
