<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2012-11-15 16:50:40 +0100 (Do, 15. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12464 $
 * 
 * $Id: exchange.php 12464 2012-11-15 15:50:40Z wallenium $
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
define('NO_MMODE_REDIRECT', true);

include_once($eqdkp_root_path . 'common.php');
$myOut = '';
ini_set('display_errors', 1);
if (registry::register('config')->get('pk_maintenance_mode')){
	if (registry::register('input')->get('format') == 'json'){
		$myOut = json_encode(array('status' => 0, 'error' => 'maintenance'));
	} else {
		$myOut = '<?xml version="1.0" encoding="UTF-8"?><response><status>0</status><error>maintenance</error></response>';
	}
	echo($myOut);
	exit;
}

if(registry::register('input')->get('out') != ''){
	
	switch (registry::register('input')->get('out')){

		case 'comments':
			if(!registry::fetch('user')->is_signedin()){
				//Check if key exists
				if (registry::register('input')->exists('key')){
					register('pm');
					$row = registry::register('plus_datahandler')->get('guildrequest_requests', 'id', array(registry::register('input')->get('attach_id', 0)));
					if($row['auth_key'] != registry::register('input')->get('key')) {
						echo('You have no permission to see this page as you are not logged in');exit;
					}
				} else {
					echo('You have no permission to see this page as you are not logged in');exit;
				}
			}
			include_once($eqdkp_root_path.'plugins/guildrequest/includes/gr_comments.class.php');
			if(registry::register('input')->get('deleteid', 0)){
				registry::register('gr_comments')->Delete('guildrequest', registry::register('input')->get('rpath'));
			}elseif(registry::register('input')->get('comment', '', 'htmlescape')){
				registry::register('gr_comments')->Save();
			}else{
				echo registry::register('gr_comments')->Content(registry::register('input')->get('attach_id', 0), 'guildrequest', registry::register('input')->get('rpath'), true);
			}
			exit;
		break;

		
	}

	if(is_file($myOut)){
			ob_end_clean();
			ob_start();
			$outdata = file_get_contents($myOut);
			echo((isset($outdata)) ? $outdata : '<?xml version="1.0" encoding="UTF-8"?><response><status>0</status><error>no data</error></response>');
	}else{
		echo '<?xml version="1.0" encoding="UTF-8"?><response><status>0</status><error>no file</error></response>';
	}
	exit;
}else{
	echo '<?xml version="1.0" encoding="UTF-8"?><response><status>0</status><error>no selection</error></response>';
	exit;
}

?>