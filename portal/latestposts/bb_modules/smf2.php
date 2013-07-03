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
 * $Id: smf2.php 12207 2012-10-06 10:41:52Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
class latestpostsmodule_smf2 {
	
	public function getBBQuery($arrPrivateforums, $black_or_white, $topicnumber) {
		// Build the db query
		$myBBquery	= "SELECT ms.ID_TOPIC as bb_topic_id, ms.subject as bb_topic_title, 
						ms.ID_MSG as bb_message_id, ms.poster_time as bb_posttime, t.num_replies as bb_replies, 
						ms.ID_MEMBER as bb_poster_id, ms.poster_name as bb_username
						FROM __messages ms, __topics t
						WHERE ms.ID_MSG = t.ID_LAST_MSG ";
		if(is_array($arrPrivateforums) && !empty($arrPrivateforums)){
		  $myBBquery	.= " AND t.ID_BOARD ".$black_or_white."(". implode(', ', $arrPrivateforums).") ";
		}
		$myBBquery	.= "ORDER BY ms.poster_time DESC LIMIT ".trim($topicnumber);
		return $myBBquery;
	}
	
	public function getBBForumQuery(){
		$myBBforumQuery = "SELECT id_board as id, name as name FROM __boards";	
		return $myBBforumQuery;
	}
	
	
	public function getBBLink($mode, $row){
		if($mode=='member'){
			return 'index.php?action=profile;u='.$row['bb_poster_id'];
		}else{
			return 'index.php?topic='.$row['bb_topic_id'].'.msg'.$row['bb_message_id'].';topicseen#new';
		}
	}
}
?>