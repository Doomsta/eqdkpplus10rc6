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
 * $Id: phpbb3.php 12207 2012-10-06 10:41:52Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class latestpostsmodule_phpbb3 {
	
	public function getBBQuery($arrPrivateforums, $black_or_white, $topicnumber) {
		// Build the db query
		$myBBquery	= "SELECT t.topic_id as bb_topic_id, t.topic_title as bb_topic_title, 
						t.topic_last_post_id as bb_last_post, t.forum_id as bb_forum_id, 
						p.post_id as bb_post_id, p.poster_id as bb_poster_id, p.post_time as bb_posttime, 
						u.user_id as bb_user_id, u.username as bb_username, t.topic_replies as bb_replies
						FROM __topics t, __forums f, __posts p, __users u
						WHERE t.topic_id = p.topic_id AND
						f.forum_id = t.forum_id AND
						t.topic_status <> 2 AND ";
		if(is_array($arrPrivateforums) && !empty($arrPrivateforums)){
			$myBBquery .= "t.forum_id ".$black_or_white."(". implode(', ', $arrPrivateforums).") AND ";
		}
		$myBBquery	.= "p.post_id = t.topic_last_post_id AND
						p.poster_id = u.user_id
						ORDER BY p.post_id DESC LIMIT ".trim($topicnumber);
						
		return $myBBquery;
	}
	
	public function getBBForumQuery(){
		$myBBforumQuery = "SELECT forum_id as id, forum_name as name FROM __forums ORDER BY left_id ASC";	
		return $myBBforumQuery;
	}
	
	
	public function getBBLink($mode, $row){
		if($mode=='member'){
			return 'memberlist.php?mode=viewprofile&u='.$row['bb_user_id'];
		}else{
			return 'viewtopic.php?f='.$row['bb_forum_id'].'&t='.$row['bb_topic_id'].'&p='.$row['bb_post_id'].'#p'.$row['bb_post_id'];
		}
	}
}
?>