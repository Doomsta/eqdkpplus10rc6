<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-12-01 00:20:24 +0100 (Sa, 01. Dez 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12525 $
 * 
 * $Id: english.php 12525 2012-11-30 23:20:24Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'latestposts'					=> 'Latest Forum Posts',
	'latestposts_name'				=> 'Latest Forum Posts',
	'latestposts_desc'				=> 'Show the recent posts of your bulletin board',
	'portal_latestposts_nomodule'	=> 'No board module selected. Please go to module settings and configure this module.!',
	'portal_latestposts_invmodule'	=> 'Wrong BB Module',
	'portal_latestposts_noselectedboards'	=> 'No forums have been selected to be displayed.',
	'pk_latestposts_bbmodule'		=> 'Forum Module',
	'pk_latestposts_amount'			=> 'Show last x Entries',
	'pk_latestposts_dbmode'			=> 'Select the database connection mode',
	'pk_latestposts_dbmode_h'		=> 'The Database connection information is only required if you select "Another DB than EQDKP"',
	'pk_latestposts_dbmode_same'	=> 'Forum is in the same db as EQDKP',
	'pk_latestposts_dbmode_new'		=> 'Forum is in another db as EQDKP',
	'pk_latestposts_dbmode_bridge'	=> 'Use connection settings of bridge',
	'pk_latestposts_dbname'			=> 'Database',
	'pk_latestposts_dbuser'			=> 'User',
	'pk_latestposts_dbpassword'		=> 'Password',
	'pk_latestposts_dbhost'			=> 'Host',
	'pk_latestposts_dbprefix'		=> 'Prefix',
	'pk_latestposts_url'			=> 'URL to the Board',
	'pk_latestposts_noentries'		=> 'No entries available',
	'pk_latestposts_connerror'		=> 'Connection Error',
	'pk_latestposts_lastpost'		=> 'Last Entry',
	'pk_latestposts_starter'		=> 'Starter',
	'pk_latestposts_posts'			=> 'Replies',
	'pk_latestposts_title'			=> 'Title',
	'pk_latestposts_trimtitle'		=> 'Trim long topic titles after x chars',
	'pk_latestposts_trimtitle_h'	=> 'Only when in left/right Module block',
	'pk_latestposts_privateforums'	=> 'Black-/Whitelist for Usergroup: ',
	'pk_latestposts_privateforums_help'	=> 'Insert here the forum IDs used by Black-/Whitelist, seperated by commas',
	'pk_latestposts_privateforums_dd_help'	=> 'Select the forums for the shown usergroup used by Black-/Whitelist',
	'pk_latestposts_linktype'		=> 'How should the topic links be opened? ',
	'pk_latestposts_blackwhite'		=> 'Black - or Whitelists',
	'pk_latestposts_blackwhite_h'	=> 'Reject the inserted Forum IDs (blacklisting) or accept them (whitelisting)',
);
?>