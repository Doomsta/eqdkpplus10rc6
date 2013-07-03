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

$guildrequestSQL = array(

  'uninstall' => array(
    1     => 'DROP TABLE IF EXISTS `__guildrequest_fields`',
	2     => 'DROP TABLE IF EXISTS `__guildrequest_requests`',
	3     => 'DROP TABLE IF EXISTS `__guildrequest_visits`',
  ),

  'install'   => array(
	1 => "CREATE TABLE `__guildrequest_fields` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` VARCHAR(50) NOT NULL,
	`name` TEXT COLLATE utf8_bin NOT NULL,
	`help` TEXT COLLATE utf8_bin NULL,
	`options` TEXT COLLATE utf8_bin DEFAULT NULL,
	`sortid` INT(10) UNSIGNED NULL DEFAULT '0',
	`required` TINYINT(3) UNSIGNED NULL DEFAULT '0',
	`in_list` TINYINT(3) UNSIGNED NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
",
	2 => "CREATE TABLE `__guildrequest_requests` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`tstamp` INT(10) NULL DEFAULT '0',
	`username` VARCHAR(255) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`auth_key` VARCHAR(255) NOT NULL,
	`lastvisit` INT(10) UNSIGNED NULL DEFAULT '0',
	`activation_key` VARCHAR(255) NULL DEFAULT NULL,
	`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`activated` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`closed` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`content` TEXT COLLATE utf8_bin NOT NULL,
	`voting_yes` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`voting_no` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`voted_user` TEXT COLLATE utf8_bin DEFAULT NULL,
	PRIMARY KEY (`id`)
)
DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
",
	
	3 => "CREATE TABLE `__guildrequest_visits` (
	`request_id` INT(10) NOT NULL,
	`user_id` INT(10) NOT NULL,
	`lastvisit` INT(10) NOT NULL,
	PRIMARY KEY (`request_id`, `user_id`),
	INDEX `request_id` (`request_id`),
	INDEX `user_id` (`user_id`)
)
DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
",)
  
  
);

?>
