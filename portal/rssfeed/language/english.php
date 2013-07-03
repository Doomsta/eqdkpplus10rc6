<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-10-02 23:43:08 +0200 (Di, 02. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12177 $
 * 
 * $Id: english.php 12177 2012-10-02 21:43:08Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'rssfeed'				=> 'RSS Feeds',
	'rssfeed_name'			=> 'RSS Feed Module',
	'rssfeed_desc'			=> 'Shows an RSS Feed in portal',
	'pk_rssfeed_limit'		=> 'Amount of feed items to show',
	'pk_rssfeed_url'		=> 'URL of the RSS Feed',
	'pk_rssfeed_nourl'		=> 'Please setup a Feed first',
	'pk_rssfeed_length'		=> 'Amount of characters from feed to show',
	'pk_rssfeed_length_h'	=> 'If the feed-module becomes extreme wide, the problem may be a destroyed HTML-Tag, because of the limited characters. If there are many characters without a white-space in that tag, there will be no new line and so the whole left-column becomes very wide.',
);
?>