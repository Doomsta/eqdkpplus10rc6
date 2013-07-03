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
 * $Id: wbb3.php 12207 2012-10-06 10:41:52Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
class latestpostsmodule_wbb3 {

	public function getBBQuery($arrPrivateforums, $black_or_white, $topicnumber) {
			// Build the db query
			$myBBquery	= "SELECT t.threadID as bb_topic_id, t.topic as bb_topic_title, 
							p.postID as bb_last_post, t.lastPostTime as bb_posttime, 
							t.replies as bb_replies, t.lastPosterID as bb_user_id, 
							t.lastPoster as bb_username
							FROM __thread t, __post p
							WHERE t.threadID = p.threadid";
			if(is_array($arrPrivateforums) && !empty($arrPrivateforums)){
				$myBBquery .= " AND t.boardID ".$black_or_white."(". implode(', ', $arrPrivateforums).")";
			}
			$myBBquery	.= " AND p.time = t.lastPostTime 
							ORDER BY t.lastPostTime DESC LIMIT ".trim($topicnumber);
						
		return $myBBquery;
	}
	
	public function getBBForumQuery(){
		$myBBforumQuery = "SELECT boardID as id, title as name FROM __board";	
		return $myBBforumQuery;
	}
	
	
	public function getBBLink($mode, $row){
		if($mode=='member'){
			return 'index.php?page=User&userID='.$row['bb_user_id'];
		}else{
			return 'index.php?page=Thread&threadID='.$row['bb_topic_id'].'&action=firstNew';
		}
	}
}
?>