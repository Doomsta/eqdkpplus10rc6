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
 * $Id: vb3.php 12207 2012-10-06 10:41:52Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
class latestpostsmodule_vb3 {

	public function getBBQuery($arrPrivateforums, $black_or_white, $topicnumber) {
		// Build the db query
		$myBBquery  = "SELECT t.threadid as bb_topic_id, t.title as bb_topic_title, 
						t.lastpostid as bb_last_post, t.forumid as bb_forum_id, 
						t.lastpost as bb_posttime, t.replycount as bb_replies,
						u.userid as bb_user_id, u.username as bb_username
						FROM __thread t, __post p, __user u
						WHERE t.threadid = p.threadid
						AND t.open = '1' AND ";
		if(is_array($arrPrivateforums) && !empty($arrPrivateforums)){
			$myBBquery .= "t.forumid ".$black_or_white."(". implode(', ', $arrPrivateforums).") AND ";
		}
		$myBBquery	.= "p.postid = t.lastpostid AND
						p.userid = u.userid
						ORDER BY t.lastpost DESC LIMIT ".trim($topicnumber);
						
		return $myBBquery;
	}
	
	public function getBBForumQuery(){
		$myBBforumQuery = "SELECT tid as id, title as name FROM __topics";	
		return $myBBforumQuery;
	}
	
	
	public function getBBLink($mode, $row){
		if($mode=='member'){
			return 'member.php?u='.$row['bb_user_id'];
		}else{
			return 'showthread.php?p='.$row['bb_last_post'].'#post'.$row['bb_last_post'];
		}
	}
}
?>