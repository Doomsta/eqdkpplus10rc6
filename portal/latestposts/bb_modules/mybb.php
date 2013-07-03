<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-11-11 19:07:23 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12435 $
 * 
 * $Id: mybb.php 12435 2012-11-11 18:07:23Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
class latestpostsmodule_mybb {

	public function getBBQuery($arrPrivateforums, $black_or_white, $topicnumber) {
			// Build the db query
			$myBBquery	= "SELECT t.tid as bb_topic_id, t.subject as bb_topic_title, 
							p.pid as bb_last_post, t.lastpost as bb_posttime, 
							t.replies as bb_replies, t.lastposteruid as bb_user_id, 
							t.lastposter as bb_username
							FROM __threads t, __posts p
							WHERE t.tid = p.tid";
			if(is_array($arrPrivateforums) && !empty($arrPrivateforums)){
				$myBBquery .= " AND t.fid ".$black_or_white."(". implode(', ', $arrPrivateforums).")";
			}
			$myBBquery	.= " AND p.dateline = t.lastpost
							ORDER BY t.lastpost DESC LIMIT ".trim($topicnumber);
						
		return $myBBquery;
	}
	
	public function getBBForumQuery(){
		$myBBforumQuery = "SELECT fid as id, name as name FROM __forums WHERE type = 'f' ORDER BY pid ASC";	
		return $myBBforumQuery;
	}
	
	
	public function getBBLink($mode, $row){
		if($mode=='member'){
			return 'member.php?action=profile&uid='.$row['bb_user_id'];
		}else{
			return 'showthread.php?tid='.$row['bb_topic_id'].'&pid='.$row['bb_last_post'].'#pid'.$row['bb_last_post'];
		}
	}
}
?>