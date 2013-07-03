<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2012-11-09 21:30:54 +0100 (Fr, 09. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12412 $
 * 
 * $Id: Ts3Viewer.php 12412 2012-11-09 20:30:54Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class Ts3Viewer extends gen_class{
	public static $shortcuts = array('config');
	protected $ip, $port, $t_port, $info, $error, $alert, $timeout, $fp, $plist, $clist, $sinfo, $connected, $noError;
	
	public function __construct() {
		// INIT variables
		$this->connected = FALSE;
		$this->noError = TRUE;
		
		// (timeout in microseconds) 1000000 is 1 second - Default: 500000
		if (isset($H_MODE) && $H_MODE) {
			$this->timeout = 500000; // 500000 fixed for hosting-mode
		} else {
			$this->timeout = ($this->config->get('pk_ts3_timeout') == '') ? 500000 : $this->config->get('pk_ts3_timeout');
		}
		
		// The Server IP (without Port)
		// Die Server IP (ohne Port)
		$this->ip = ($this->config->get('pk_ts3_ip') == '') ? '127.0.0.1' : $this->config->get('pk_ts3_ip');


		// The port - Default: 9987
		// Der Port - Standart: 9987
		$this->port = ($this->config->get('pk_ts3_port') == '') ? '9987' : $this->config->get('pk_ts3_port');

		// The Telnet Port of your Server - Default: 10011
		// Der Telnet Port deines Servers - Standart: 10011
		$this->t_port = ($this->config->get('pk_ts3_telnetport') == '') ? '10011' : $this->config->get('pk_ts3_telnetport');

		// The ID from your Virtual Server - Default: 1
		// Die ID deines Server - Standart - 1
		$this->sid = ($this->config->get('pk_ts3_id') == '') ? '1' : $this->config->get('pk_ts3_id');
		
		$this->info['hide_spacer'] = (int)$this->config->get('pk_ts3_hide_spacer');
		
		// Shows banner if URL is avaible in TS - Yes=1 / No=0
		// Zeige das Banner, welches du im TS eingestellt hast - Ja=1 / Nein=0
		$this->info['banner'] = $this->config->get('pk_ts3_banner');
		
		// Shows join-link - Yes=1 / No=0
		// Zeige join-Link - Ja=1 / Nein=0
		$this->info['join'] = $this->config->get('pk_ts3_join');
		
		//Linktext des join-Links
		$this->info['jointext'] = $this->config->get('pk_ts3_jointext');

		// Shows groupinfo at the bottom - Yes=1 / No=0
		// Zeig unter der Tabelle eine Übersicht der Gruppen an - Ja=1 / Nein=0
		$this->info['legend'] = $this->config->get('pk_ts3_legend');

		// If you want to abridge the usernames, set this to the desired size - No cut = 0
		// Wenn du die Usernamen auf eine bestimmte Länge kürzen willst, gib hier die Anzahl der Zeichen ein - Kein Kürzen = 0
		$this->info['ts3_cut_names'] = $this->config->get('pk_ts3_cut_names');

		// If you want to abridge the channelnames, set this to the desired size - No cut = 0
		// Wenn du die Channelnamen auf eine bestimmte Länge kürzen willst, gib hier die Anzahl der Zeichen ein - Kein Kürzen = 0
		$this->info['ts3_cut_channel'] = $this->config->get('pk_ts3_cut_channel');
		
		// Show only populated channels - Yes=1 / No=0
		// Zeige nur bevölkerte Kanäle - Ja=1 / Nein=0
		$this->info['populated_only'] = $this->config->get('pk_only_populated_channel');

		// Show Online User / Possible Users - Yes=1 / No=0
		// Zeige die Anzahl der Online User und möglichen User an - Ja=1 / Nein=0
		$this->info['useron'] = $this->config->get('pk_ts3_useron');

		//Show a statistic box under the TS viewer. - Yes=1 / No=0
		//Zeigt eine Statistikbox unter dem TS Viewer - Ja=1 / Nein=0
		$this->info['stats'] = $this->config->get('pk_ts3_stats');

		//You can choose wich serverinfos will shown and change the label - Yes=1 / No=0
		//Du kannst Auswählen welcheServerinfo gezeigt werden soll und welche nicht. Ausserdem kannst Du die Bezeichnung ändern
		
		$this->info['serverinfo']['virtualserver_platform']['show'] = $this->config->get('pk_ts3_stats_showos'); //Show on wich OS TS3 run
		$this->info['serverinfo']['virtualserver_platform']['label'] = 'TS3 OS'; 

		$this->info['serverinfo']['virtualserver_version']['show'] = $this->config->get('pk_ts3_stats_version'); //Show the TS3 server version
		$this->info['serverinfo']['virtualserver_version']['label'] = 'TS3 Version'; 
		
		$this->info['serverinfo']['virtualserver_channelsonline']['show'] = $this->config->get('pk_ts3_stats_numchan'); //Show the number of channels
		$this->info['serverinfo']['virtualserver_channelsonline']['label'] = 'Channnels'; 
		
		$this->info['serverinfo']['virtualserver_uptime']['show'] = $this->config->get('pk_ts3_stats_uptime'); //Show the server uptime since the last restart
		$this->info['serverinfo']['virtualserver_uptime']['label'] = 'Uptime';

		$this->info['serverinfo']['virtualserver_created']['show'] = $this->config->get('pk_ts3_stats_install'); //Show when the server was installed
		$this->info['serverinfo']['virtualserver_created']['label'] = 'Online since';

		//Server Groups

		//to add a group you have to make 2 entries, ts3_replace NAME with the name of the group,
		//ID with the Group ID (you find the ID under Server Groups) and PIC with the name of
		//the groupimage (copy the image in the images folder. The size from the image should
		//be 16 x 16
		//$this->info['sgroup'][ID]['n'] = 'NAME';
		//$this->info['sgroup'][ID]['p'] = 'PIC';
		
		//um eine Gruppe hinzuzufügen, änder NAME in den Namen der Gruppe,
		//ID in die Gruppen ID (siehst du im Clienten unter Server Groups) und PIC mit dme Namen 
		//des Gruppenbild (kopiere das bild in den image Ordner, die größe sollte nicht mehr als 16px x 16px
		//betragen
		//$this->info['sgroup'][ID]['n'] = 'NAME';
		//$this->info['sgroup'][ID]['p'] = 'PIC';

		$this->info['sgroup'][6]['n'] = 'Serveradmin';
		$this->info['sgroup'][6]['p'] = 'sa.png';

		//CHANNEL GROUPS
		$this->info['cgroup'][5]['n'] = 'Channeladmin';
		$this->info['cgroup'][5]['p'] = 'ca.png';

		$this->info['cgroup'][6]['n'] = 'Channel Operator';
		$this->info['cgroup'][6]['p'] = 'co.png';
	}
	
	public function __destruct() {
		//close the socket
		fclose($this->fp);
		parent::__destruct();
	}
	
	public function gethtml(){
		$htmlout = '';
		if ($this->noError) {
			// if no errors occured generate the html-output
			$htmlout	.= '<div id="tsbody">';
			$htmlout	.= '   <div style="text-align:center">';
			$htmlout	.= $this->banner();
			$htmlout	.= $this->link();
			$htmlout	.= '</div>';
			$htmlout	.= '<div id="tscont">';
			$htmlout	.= '<div class="tsca"><img src="' . $this->root_path . 'portal/teamspeak3/TeamSpeakViewer/tsimages/serverimg.png'.'" alt="'.$this->replace($this->sinfo['virtualserver_welcomemessage']).'" title="'.$this->replace($this->sinfo['virtualserver_welcomemessage']).'"/></div>';
			$htmlout	.= '<div class="tsca">'.$this->replace($this->sinfo['virtualserver_name']).'</div>';
			$htmlout	.= '<div style="clear:both"></div>';
			$htmlout	.= $this->buildtree('0', '');
			$htmlout	.= $this->useron();
			$htmlout	.= $this->build_legend();
			$htmlout	.= $this->stats();
			$htmlout	.= $this->alerts();
			$htmlout	.= '</div></div>';
		}else{
			// in case of errors just output the error-strings and alerts
			$htmlout	.= $this->errors();
			$htmlout	.= $this->alerts();
		}
		return $htmlout;
	}
	
	public function connect(){
		// establish connection to ts3
		$errno = ''; $errstr = '';
		$this->fp = @fsockopen($this->ip, $this->t_port, $errno, $errstr, 1);
		if ($this->fp) {
			stream_set_timeout($this->fp, 0, $this->timeout);
			$msg = $this->read();
			if(strpos($msg, 'TS3') === FALSE){
				$this->error[] = 'Server seems to be no TS3';
				$this->noError = FALSE;
				$this->connected = FALSE;
				return false;
			} else {
				$this->connected = TRUE;
				return true;
			}
		} else {
			$this->error[] = 'Can not connect to the server ('.$errno.')';
			$this->noError = FALSE;
			$this->connected = FALSE;
			return false;
		}
	}
	
	public function disconnect(){
		//send quit-command to the server
		$cmd = "quit\n";
		fputs($this->fp, $cmd);
		$this->connected = FALSE;
	}
	
	public function query(){
		//do a normal query of relevant server information
		$this->set_sid();
		$this->query_sinfo();
		$this->query_channels();
		$this->query_clients();
	}
	
	protected function set_sid(){
		//sets the sid to use
		if (!($this->connected and $this->noError)) {return;}
		if ($this->sid == '-1') {
			//try port
			$cmd = "use port=".$this->port."\n";
		} else {
			//use sid
			$cmd = "use sid=".$this->sid."\n";
		}
		$select = $this->sendCmd($cmd);
	}
	
	protected function query_sinfo(){
		//querys the server for some general infos
		if (!($this->connected and $this->noError)) {return;}
		$cmd = "serverinfo\n";
		if(!($info = $this->sendCmd($cmd))){
			$this->error[] = 'No Serverstatus';
		}else{
			$this->sinfo = $this->splitInfo($info);
		}
	}
	
	protected function query_channels(){
		//get the channel list
		if (!($this->connected and $this->noError)) {return;}
		$cmd = "channellist -topic -flags -voice -limits\n";
		if(!($clist_t = $this->sendCmd($cmd))){
			$this->error[] = 'No Channellist';
		}else{
			$clist_t = $this->splitInfo2($clist_t);
			foreach ($clist_t as $var) {
				$this->clist[] = $this->splitInfo($var);
			}
		}
	}
	
	protected function query_clients(){
		//get the client list
		if (!($this->connected and $this->noError)) {return;}
		$cmd = "clientlist -uid -away -voice -groups\n";
		if(!($plist_t = $this->sendCmd($cmd))){
			$this->error[] = 'No Playerlist';
		}else{
			$plist_t = $this->splitInfo2($plist_t);
			foreach ($plist_t as $var) {
				if(strpos($var, 'client_type=0') !== FALSE) {
					$this->plist[] = $this->splitInfo($var);
				}
			}
			if($this->plist != ''){
				foreach ($this->plist as $key => $var) {
					$temp = '';
					if(strpos($var['client_servergroups'], ',') !== FALSE){
						$temp = explode(',', $var['client_servergroups']);
					}else{
						$temp[0] = $var['client_servergroups'];
					}
					$t = '0';
					foreach ($temp as $t_var) {
						if($t_var == '6'){
							$t = '1';
						}
					}
					if($t == '1'){
						$this->plist[$key]['s_admin'] = '1';
					}else{
						$this->plist[$key]['s_admin'] = '0';
					}
				}
				usort($this->plist, "ts3_cmp_group");
				usort($this->plist, "ts3_cmp_admin");
			}

		}
	}

	protected function link(){
		//generate the join-link
		$return = '';
		if($this->info['join'] == 1){
			$return = '<h3><a href="ts3server://'.$this->ip.'?port='.$this->port.'">'.$this->info['jointext'].'</a></h3>';
		}
		return $return;
	}
	
	protected function parse_error($msg){
		//add infos to known error-codes
		if (strpos($msg, 'error id=3329') !== false) {
			$this->error[] = 'Queryclient has ban, check flooding settings. Please take a look at the <a href="'.EQDKP_WIKI_URL.'/de/index.php/Teamspeak3">wiki</a>';
		}
		if (strpos($msg, 'error id=3331') !== false) {
			$this->error[] = 'Queryclient has ban, check flooding settings. Please take a look at the <a href="'.EQDKP_WIKI_URL.'/de/index.php/Teamspeak3">wiki</a>';
		}
		if (strpos($msg, 'error id=2568') !== false) {
			$this->error[] = 'Missing permissions to query the server, check your permission-settings in your TS3-Server';
		}
		return $msg;
	}
	
	protected function sendCmd($cmd){
		//sends a command to ts3 and gets the answer
		$msg = '';
		if ($this->connected and $this->noError){
			fputs($this->fp, $cmd);
			$msg = $this->read();
		} else {
			$msg = "No Connection or Connection lost";
		}
		if(!strpos($msg, 'msg=ok')){
			if (strlen($msg) > 0 and (strpos($msg, 'msg='))) {
				$this->error[] = $this->parse_error($this->replace($msg));
			}
			$this->noError = FALSE;
			return false;
		}else{
			return $msg;
		}
	}

	protected function read(){
		//read the answer from stream
		$msg = '';
		do {
			$msg .= fgets($this->fp);
			$meta = stream_get_meta_data($this->fp);
		} while (($meta['unread_bytes'] > 0) and (!$meta['timed_out']) and (strpos($msg, 'msg=ok') === FALSE) and (trim($msg) != "TS3"));
		if ($meta['timed_out']) {
			$this->alert[] = 'query timed out';
		}
		return $msg;
	}

	protected function splitInfo($info){
		//parses the output
		$info = trim(str_replace('error id=0 msg=ok', '', $info));
		$info = explode(' ', $info);
		foreach ($info as $var) {
			if(strpos($var, '=')=== FALSE){
				$return[$var] = '';
			}else{
				$return[substr($var, 0, (strpos($var, '=')))] = substr($var, (strpos($var, '=')+1));
			}
		}
		return $return;
	}

	protected function splitInfo2($info){
		//parses the output
		$info = trim(str_replace('error id=0 msg=ok', '', $info));
		$info = explode('|', $info);
		return $info;
	}

	protected function num_clients_recursive($chan_id){
		//returns the number of clients in the channel with id=$chan_id and all subchannels
		$count = 0;
		foreach ($this->clist as $key => $var) {
			if($var['cid'] == $chan_id){
				$count += $var['total_clients'];
			}
			if($var['pid'] == $chan_id){
				$count += $this->num_clients_recursive($var['cid'], $this->clist);
			}
		}
		return $count;
	}

	protected function buildtree($id,$platzhalter){
		//builds up the tree of channels and users in html
		$return = '';
		if($this->noError){
			foreach ($this->clist as $key => $var) {
				if($var['pid'] == $id){
				
					if ($this->channelIsSpacer($var)){
						if (!$this->info['pk_ts3_hide_spacer']){
							$SpacerType = $this->channelSpacerGetType($var['channel_name']);
							$SpacerAlign = $this->channelSpacerGetAlign($var['channel_name']);
							if ($SpacerType == 'custom'){
								$return .='<div class="tsleer">&nbsp;</div>';
								$channelname = substr($var['channel_name'], strpos($var['channel_name'], ']')+1);
								switch($SpacerAlign){
									case 'left': $return .= '<div style="text-align:left">'.htmlspecialchars($this->cut_channel($channelname)).'</div>'; break;
									case 'right': $return .= '<div style="text-align:right">'.htmlspecialchars($this->cut_channel($channelname)).'</div>'; break;
									case 'center': $return .= '<div style="text-align:center">'.htmlspecialchars($this->cut_channel($channelname)).'</div>'; break;
									case 'repeat': 
										$channelname = str_repeat($channelname, 5);
										$return .= '<div style="text-align:center">'.htmlspecialchars($this->cut_channel($channelname)).'</div>';
									break;
								}
							} else {
								switch($SpacerType){
									case 'solidline': $return .= '<div style="border-bottom:1px solid;margin-left:20px; margin-top:2px; margin-bottom:2px; height:2px;">&nbsp;</div>'; break;
									case 'dashdotline':
									case 'dashline': $return .= '<div style="border-bottom:1px dashed;margin-left:20px; margin-top:2px; margin-bottom:2px; height:2px;">&nbsp;</div>'; break;
									case 'dashdotdotline':
									case 'dotline': $return .= '<div style="border-bottom:1px dotted;margin-left:20px; margin-top:2px; margin-bottom:2px; height:2px;">&nbsp;</div>'; break;
								}
							}
							$return .= '<div style="clear:both"></div>'.$this->buildtree($var['cid'],$platzhalter.'<div class="tsleer">&nbsp;</div>');
						}
						
					} else {
			
					
						if(($this->info['populated_only'] != '1') or ($this->num_clients_recursive($var['cid']) >= '1')){
							$return .= $platzhalter;
							$return .= '<div class="tsleer">&nbsp;</div>';
							$return .= '<div class="tsca"><img src="' . $this->root_path . 'portal/teamspeak3/TeamSpeakViewer/tsimages/channel.png'.'" alt="'.$this->replace($var['channel_topic']).'" title="'.$this->replace($var['channel_topic']).'" /></div>';
							$return .= '<div class="tsca">'.htmlspecialchars($this->cut_channel($var['channel_name'],$this->info)).'</div>';
							if($var['channel_flag_default'] == 1){
								$return .= '<div class="tsca" style="float:right;"><img src="' . $this->root_path . 'portal/teamspeak3/TeamSpeakViewer/tsimages/home.png'.'" alt="'.$this->replace($var['channel_topic']).'" title="'.$this->replace($var['channel_topic']).'" /></div>';
							}

							if($var['channel_flag_password'] == 1){
								$return .= '<div class="tsca" style="float:right;"><img src="' . $this->root_path . 'portal/teamspeak3/TeamSpeakViewer/tsimages/schloss.png'.'" alt="'.$this->replace($var['channel_topic']).'" title="'.$this->replace($var['channel_topic']).'" /></div>';
							}

							$return .= '<div style="clear:both"></div>';
						}
						if($var['total_clients'] >= '1'){
							if($this->plist != ''){
								foreach($this->plist as $u_key => $u_var){
									if($u_var['cid'] == $var['cid']){
										$p_img = 'player.png';
										if($u_var['client_input_muted'] == '1'){
											$p_img = 'mic.png';
										}
										if($u_var['client_output_muted'] == '1'){
											$p_img = 'head.png';
										}
										if($u_var['client_away'] == '1'){
											$p_img = 'away.png';
										}
										$g_img = '';
										$g_temp = '';
										if(strpos($u_var['client_servergroups'], ',') !== FALSE){
											$g_temp = explode(',', $u_var['client_servergroups']);
										}else{
											$g_temp[0] = $u_var['client_servergroups'];
										}
										foreach ($g_temp as $sg_var) {
											if(isset($this->info['sgroup'][$sg_var]['p'])){
												$g_img .= '<div class="tsca" style="float:right"><img src="' . $this->root_path . 'portal/teamspeak3/TeamSpeakViewer/tsimages/'.''.$this->info['sgroup'][$sg_var]['p'].'" alt="'.$this->info['sgroup'][$sg_var]['n'].'" /></div>';
											}
										}
										if(isset($this->info['cgroup'][$u_var['client_channel_group_id']]['p'])){
											if(isset($this->info['cgroup'][$u_var['client_channel_group_id']]['p'])){
												$g_img .= '<div class="tsca" style="float:right"><img src="' . $this->root_path . 'portal/teamspeak3/TeamSpeakViewer/tsimages/'.''.$this->info['cgroup'][$u_var['client_channel_group_id']]['p'].'" alt="'.$this->info['cgroup'][$u_var['client_channel_group_id']]['n'].'" /></div>';
											}
										}
										$return .= $platzhalter.'<div class="tsleer">&nbsp;</div><div class="tsleer">&nbsp;</div><div class="tsca"><img src="'.$this->root_path . 'portal/teamspeak3/TeamSpeakViewer/tsimages/'.''.$p_img.'" alt="Player" /></div><div class="tsna">'.htmlspecialchars($this->cut_names($u_var['client_nickname'],$this->info), ENT_COMPAT | ENT_HTML401, 'UTF-8').'</div>'.$g_img.'<div style="clear:both"></div>';
									}
								}
							}
						}
						$return .= $this->buildtree($var['cid'],$platzhalter.'<div class="tsleer">&nbsp;</div>');
					}
				}
			}
		}
		return $return;
	}
	
	  public function channelIsSpacer($channel){
		return (preg_match("/\[.*spacer.*\]/", $channel['channel_name']) && (int)$channel["channel_flag_permanent"] && !(int)$channel["pid"]) ? TRUE : FALSE;
	  }
	
	protected function replace($var){
		//some replacements for parsing and displaying ts3-answers
		$search[] = chr(92).chr(92);
		$replace[] = chr(92);
		$search[] = '\/';
		$replace[] = '/';
		$search[] = '\s';
		$replace[] = ' ';
		$search[] = '\p';
		$replace[] = '|';
		$search[] = '[URL]';
		$replace[] = '';
		$search[] = '[/URL]';
		$replace[] = '';
		$search[] = '[b]';
		$replace[] = '';
		$search[] = '[/b]';
		$replace[] = '';

		return str_replace($search, $replace, $var);
	}
	
	protected function channelSpacerGetAlign($channelname){

		if(!preg_match("/\[(.*)spacer.*\]/", $channelname, $matches) || !isset($matches[1]))
		{
		  return "";
		}

		switch($matches[1])
		{
		  case "*":
			return 'repeat';

		  case "c":
			return 'center';

		  case "r":
			return 'right';

		  default:
			return 'left';
		}
	}
	
	protected function channelSpacerGetType($channelname){
		$section = substr($channelname, strpos($channelname, ']')+1);
		switch($section)
		{
		  case "___":
			return 'solidline';

		  case "---":
			return 'dashline';

		  case "...":
			return 'dotline';

		  case "-.-":
			return 'dashdotline';

		  case "-..":
			return 'dashdotdotline';

		  default:
			return 'custom';
		}
	}
	
	

	protected function cut_channel($var){
		//cuts channel-names if option is set
		$var = $this->replace($var);
		if($this->info['ts3_cut_channel'] >= '1'){
			$count = strlen($var);
			if($count > $this->info['ts3_cut_channel']){
				$pos = $this->info['ts3_cut_channel']-3;
				$var = substr($var, 0, $pos).'...';

			}
		}
		return $var;
	}
	
	protected function replace_spacer($var){
	
	}

	protected function cut_names($var){
	//cuts user-names if option is set
		$var = $this->replace($var);
		if($this->info['ts3_cut_names'] >= '1'){
			$count = strlen($var);
			if($count > $this->info['ts3_cut_names']){
				$pos = $this->info['ts3_cut_names']-3;
				$var = substr($var, 0, $pos).'...';
			}
		}
		return $var;
	}

	protected function build_legend(){
		//generates html-ouput for the group legend
		$return = '';
		if($this->info['legend'] == '1'){
			$return .= '<div id="legend" ><h3>Legend</h3>';
			foreach ($this->info['sgroup'] as $var) {
				$return .= '<div class="tsle"><img src="' . $this->root_path . 'portal/teamspeak3/TeamSpeakViewer/tsimages/'.''.$var['p'].'" alt="'.$var['n'].'" /></div>';
				$return .= '<div class="tsle">'.$var['n'].'</div>';
				$return .= '<div style="clear:both"></div>';
			}
			foreach ($this->info['cgroup'] as $var) {
				$return .= '<div class="tsle"><img src="' . $this->root_path . 'portal/teamspeak3/TeamSpeakViewer/tsimages/'.''.$var['p'].'" alt="'.$var['n'].'" /></div>';
				$return .= '<div class="tsle">'.$var['n'].'</div>';
				$return .= '<div style="clear:both"></div>';
			}
			$return .= '</div>';
		}
		return $return;
	}

	protected function useron(){
		//generates html-output for the number of user online in ts3
		$return = '';
		if($this->info['useron'] == 1 && isset($this->sinfo['virtualserver_clientsonline'])){
			$return .= '<div class="useron">User online: '.($this->sinfo['virtualserver_clientsonline']-$this->sinfo['virtualserver_queryclientsonline']).'/'.$this->sinfo['virtualserver_maxclients'].' </div>';
		}
		return $return;
	}

	protected function banner(){
		//generates html-output for the ts3-banner if selected in options
		$return = '';
		if($this->info['banner'] == 1 && isset($this->sinfo['virtualserver_hostbanner_gfx_url']) && $this->sinfo['virtualserver_hostbanner_gfx_url'] != ''){
			$return .= '<img id="tsbanner" src="'.$this->replace($this->sinfo['virtualserver_hostbanner_gfx_url']).'" alt="TS Banner" />';
		}
		return $return;
	}

	protected function stats(){
		//generates html-output for some ts3-stats
		$return = '';
		$tag = floor($this->sinfo['virtualserver_uptime']/60/60/24);
		$std = ($this->sinfo['virtualserver_uptime']/60/60)%24;
		$min = ($this->sinfo['virtualserver_uptime']/60)%60;
		$this->sinfo['virtualserver_created'] = date('d M Y', $this->sinfo['virtualserver_created']);
		$this->sinfo['virtualserver_uptime'] = $tag.' Days '.$std.' Hours '.$min.' Min';
		if($this->info['stats'] == 1){
			$return .= '<div id="ts3stats"><h3>Statistic</h3><table>';
			foreach ($this->info['serverinfo'] as $key => $var){
				if($var['show'] == 1){
					$return .= '<tr><td style="font-weight:bold">'.$var['label'].':</td><td>'.$this->replace($this->sinfo[$key]).'</td></tr>';
				}
			}
			$return .= '</table></div>';
		}
		return $return;
	}

	protected function errors(){
		//generates html-output to display errors
		$return = '';
		if (isset($this->error[0])){
			$return .= '<div id="ts3errors"><h3>Errors</h3>';
			foreach ($this->error as $var) {
				$return .= $var.'<br />';
			}
			$return .= '</div>';
		}
		return $return;
	
	}
	
	protected function alerts(){
	//generates html-output to display alerts
		$return = '';
		if (isset($this->alert[0])){
			$return .= '<div id="ts3alerts"><h3>Alerts</h3>';
			foreach ($this->alert as $var) {
				$return .= $var.'<br />';
			}
			$return .= '</div>';
		}
		return $return;
	}

} // End Ts3Viewer-Class

// Compare-Functions for User-Sorting
function ts3_cmp_admin($a, $b){
	return strcmp($b["s_admin"], $a["s_admin"]);
}
function ts3_cmp_group($a, $b){
	return strcmp($b["client_channel_group_id"], $a["client_channel_group_id"]);
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Ts3Viewer', Ts3Viewer::$shortcuts);
?>