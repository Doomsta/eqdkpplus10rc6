<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-10-06 12:41:52 +0200 (Sa, 06. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy_leon $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12207 $
 * 
 * $Id: e107.php 12207 2012-10-06 10:41:52Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
class latestpostsmodule_e107 {

	public function getBBQuery($arrPrivateforums, $black_or_white, $topicnumber) {
			// Build the db query
			$myBBquery	= "SELECT t.thread_id as bb_topic_id, t.thread_name as bb_topic_title, 
				t.thread_forum_id as bb_forum_id, t.thread_datestamp as bb_posttime, t.thread_total_replies as bb_replies, 
				t.thread_user as bb_poster_id, m.user_name as bb_username
				FROM __forum_t t, __user m
				WHERE t.thread_lastuser = m.user_id ";
				if(is_array($arrPrivateforums) && !empty($arrPrivateforums)){
					$myBBquery .= " AND t.thread_forum_id ".$black_or_white."(". implode(', ', $arrPrivateforums).") ";
				}
				$myBBquery .= "ORDER BY t.thread_lastpost DESC LIMIT ".trim($topicnumber);
						
		return $myBBquery;
	}
	
	public function getBBLink($mode, $row){
		return 'e107_plugins/forum/forum_viewtopic.php?'.$row['bb_topic_id'];
	}
}
?>