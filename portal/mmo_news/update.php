<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2011-08-30 13:53:08 +0200 (Di, 30. Aug 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11111 $
 * 
 * $Id: update.php 11111 2011-08-30 11:53:08Z wallenium $
 */

define('EQDKP_INC', true);

$eqdkp_root_path = './../../';
include_once($eqdkp_root_path.'common.php');
include_once($eqdkp_root_path . 'portal/mmo_news/mmo_news_rss.class.php');
$mmo_news = registry::register('mmo_news_rss', array(false));
$mmo_news->updateRSS();
?>