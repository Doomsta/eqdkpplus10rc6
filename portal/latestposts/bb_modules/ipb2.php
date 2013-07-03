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
 * $Id: ipb2.php 12207 2012-10-06 10:41:52Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
class latestpostsmodule_ipb2 {

	public function getBBQuery($arrPrivateforums, $black_or_white, $topicnumber) {
		$myBBquery	= "SELECT t.tid as bb_topic_id, t.title as bb_topic_title, 
			t.forum_id as bb_forum_id, t.last_post as bb_posttime, t.posts as bb_replies, 
			t.last_poster_id as bb_poster_id, m.name as bb_username
			FROM __topics t, __members m
			WHERE t.last_poster_id = m.id ";
		if(is_array($arrPrivateforums) && !empty($arrPrivateforums)){
			$myBBquery .= " AND t.forum_id ".$black_or_white."(". implode(', ', $arrPrivateforums).") ";
		}
		$myBBquery	.= "ORDER BY t.last_post DESC LIMIT ".trim($topicnumber);
						
		return $myBBquery;
	}
	
	public function getBBLink($mode, $row){
		return 'index.php?'.(($mode=='member') ? 'showuser='.$row['bb_poster_id'] : 'showtopic='.$row['bb_topic_id'].'&view=getlastpost');
	}
}
?>