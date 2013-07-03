<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-08-09 10:00:07 +0200 (Di, 09. Aug 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: Aderyn $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 10949 $
 *
 * $Id: sql.php 10949 2011-08-09 08:00:07Z Aderyn $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}

$shoutboxSQL = array(

  'uninstall' => array(
    '1'     => 'DROP TABLE IF EXISTS `__shoutbox`',
  ),

  'install'   => array(
    '1'     => 'CREATE TABLE IF NOT EXISTS `__shoutbox` (
                  `shoutbox_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `user_or_member_id` SMALLINT(5) NOT NULL DEFAULT \'-1\',
                  `shoutbox_date` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
                  `shoutbox_text` TEXT COLLATE utf8_bin DEFAULT NULL,
                  PRIMARY KEY (`shoutbox_id`)
                ) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;',
  ),
);

?>
