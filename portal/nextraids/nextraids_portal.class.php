<?php
if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class nextraids_portal extends portal_generic {
	
	public static function __shortcuts() {
		$shortcuts = array('user', 'pdh', 'core', 'time', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}
	private $colors 	= array(
					0 => '#33FF00',
					1 => '#FFFF00',
					2 => '#FF0000',
					3 => ''
		);
	protected $path		= 'nextraids';
	protected $data		= array(
		'name'			=> 'Nextraids',
		'version'		=> '3.0.1',
		'author'		=> 'WalleniuM Mod by Doom',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Shows the future raids in the portal',
	);
	protected $positions = array('left1', 'left2', 'right');
	protected $settings	= array(
		'nr_nextraids_limit'	=> array(
			'name'		=> 'nr_nextraids_limit',
			'language'	=> 'nr_nextraids_limit',
			'property'	=> 'text',
			'size'		=> '2',
		),
		'nr_nextraids_hideclosed'	=> array(
			'name'		=> 'nr_nextraids_hideclosed',
			'language'	=> 'nr_nextraids_hideclosed',
			'property'	=> 'checkbox',
		),
	);
	protected $install	= array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '1',
	);

	public function output() {
		// Load the event data
		$lastdate = null;
		$caleventids	= $this->pdh->sort($this->pdh->get('calendar_events', 'id_list', array(false, $this->time->time)), 'calendar_events', 'date', 'asc');
		$out = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="noborder nextraid_table">';

		$raidcal_status = unserialize($this->config->get('calendar_raid_status'));
		$raidstatus = array();
		if(is_array($raidcal_status)){
			foreach($raidcal_status as $raidcalstat_id){
				if($raidcalstat_id != 4){
					$raidstatus[$raidcalstat_id]	= $this->user->lang(array('raidevent_raid_status', $raidcalstat_id));
				}
			}
		}

		$count_i = 1;
		if(is_array($caleventids) && count($caleventids) > 0){
			foreach($caleventids as $eventid)
			{
				//doom
				$query = $this->db->query('SELECT notes FROM `eqdkp10_calendar_events` WHERE id = "'.$eventid.'"');
				while ($row = $this->db->fetch_row($query)) 
					$note = $row['notes'];
				
				
				if(substr($note, 0,4) == 'INFO')
				{
					$note = substr($note, 4);
					$end = strpos($note, 'INFO');
					$note2 = substr($note,0, $end);
					
				}
				else
					$note = null;
					
				$eventextension	= $this->pdh->get('calendar_events', 'extension', array($eventid));
				$raidclosed		= ($this->pdh->get('calendar_events', 'raidstatus', array($eventid)) == '1') ? true : false;
				
				$this->time->user_date($this->pdh->get('calendar_events', 'time_start', array($eventid)));
				if($eventextension['calendarmode'] != 'raid')
					continue;

				// switch closed raids if enabled
				if($this->config->get('nr_nextraids_hideclosed') && $raidclosed)
					continue;

				$own_status		= false;
				$count_status	= $count_array = '';
				$raidplink		= $this->root_path.'calendar/viewcalraid.php'.$this->SID.'&amp;eventid='.stripslashes($eventid);

				// Build the Attendee Array
				$attendees = array();
				$attendees_raw = $this->pdh->get('calendar_raids_attendees', 'attendees', array($eventid));
				if(is_array($attendees_raw)){
					foreach($attendees_raw as $attendeeid=>$attendeerow){
						$attendees[$attendeerow['signup_status']][$attendeeid] = $attendeerow;
					}
				}

				// Build the guest array
				$guests = '';
				if($this->config->get('calendar_raid_guests') == 1){
					$guestarray = $this->pdh->get('calendar_raids_guests', 'members', array($eventid));
					if(is_array($guestarray)){
						foreach($guestarray as $guest_row){
							$guests[] = $guest_row['name'];
						}
					}
				}
				// get the status counts
				$counts = '';
				foreach($raidstatus as $statusid=>$statusname){
					$counts[$statusid]  = ((isset($attendees[$statusid])) ? count($attendees[$statusid]) : 0);
				}
				$guest_count	= (is_array($guests)) ? count($guests) : 0;
				if(isset($counts[0])){
					$counts[0]		= $counts[0] + $guest_count;
				}

				$signinstatus = $this->pdh->get('calendar_raids_attendees', 'html_status', array($eventid, $this->user->data['user_id']));
				$out .= '<tr class="row1 nohover">';
				 
				//doom
				if ( $lastdate != $this->time->user_date($this->pdh->get('calendar_events', 'time_start', array($eventid))) ) 
				{
					$out .=	'	<td colspan="2" style="background-color: rgb(4, 15, 22);">
									<span style="float:left;font-weight:bold;">
										 
										'.$this->time->user_date($this->pdh->get('calendar_events', 'time_start', array($eventid))).'										
								</span>';								
				}						
				$lastdate = $this->time->user_date($this->pdh->get('calendar_events', 'time_start', array($eventid)));
				$out .= '
				<span style="float: right;width: 24px;">
								</span>
							</td>
						</tr>
						<tr class="row2 nowrap">
							<td valign="top" align="center" width="44">
							<a href="'.$raidplink.'">'.$this->pdh->get('event', 'html_icon', array($eventextension['raid_eventid'], 40)).'</a>
							</td>
							<td valign="top">';
				if($raidclosed)
				{
					$out .= '<div style="text-decoration: line-through;">'.$this->pdh->get('event', 'name', array($eventextension['raid_eventid'])).' ('.$eventextension['attendee_count'].') </div>';
				}
				else
				{
					$out .= '<a href="'.$raidplink.'">'.$this->pdh->get('event', 'name', array($eventextension['raid_eventid'])).' </a><br/>'; //('.$eventextension['attendee_count'].')
				}	
				//raid note part2
				if(!empty($note))
					$out .= '<span style="color: #c0504e; font-variant: small-caps; font-weight: bold;">'.$note2.'</span><br />';
					
		
				$out .= '<b>'.$this->time->user_date($this->pdh->get('calendar_events', 'time_start', array($eventid)), false, true).'-'.$this->time->user_date($this->pdh->get('calendar_events', 'time_end', array($eventid)), false, true).'         ';
				if (is_array($counts))
				{
					//$out .= '<div style="text-indent:10px;">';

					foreach($counts as $countid=>$countdata){
						//eqdkp$out .= '<span class="status'.$countid.'">'.$raidstatus[$countid].': '.$countdata.'</span><br/>';
						//doom mod
						$out .= '<font color="'.$this->colors[$countid].'">'.$countdata.'</font>';
						if ($countid != 3) $out .= '<font> / </font>';

					}
					$chars = join(',',$this->pdh->get('member', 'connection_id', array($this->user->data['user_id'])));
					$query = $this->db->query('SELECT DISTINCT eqdkp10_members.member_name, eqdkp10_members.member_class_id as class_id, signup_status, role_name, note
												FROM eqdkp10_members
												JOIN eqdkp10_calendar_raid_attendees ON (eqdkp10_calendar_raid_attendees.member_id = eqdkp10_members.member_id)
												JOIN eqdkp10_roles ON (eqdkp10_roles.role_id = eqdkp10_calendar_raid_attendees.member_role)  
												WHERE calendar_events_id = "'.$eventid.'" 
													AND eqdkp10_calendar_raid_attendees.member_id IN ('.$chars.') 
												LIMIT 0, 1'
											);
					$signinstatus = $this->pdh->get('calendar_raids_attendees', 'html_status', array($eventid, $this->user->data['user_id']));
					while ($row = $this->db->fetch_row($query)) 
					{
						$out .= '<br />'.$signinstatus.'<span class="normal  class_'.$row['class_id'].'">'.$row["member_name"].'</span>';
						if($this->user->check_group(7, false))
						{
							$out .= ' ('.$row["role_name"].')';
							if(!empty($row['note']))
							{
								$out .= '<br /></b><b>Notiz:</b><br /> '.wordwrap(substr($row['note'],0,255), 20, "<br />", true);
							}
						}
					}	
				}
				$out .= '</div>';
				$out .= "</tr>";

				// end the foreach if x raids are reached
				$tillvalue = ($this->config->get('nr_nextraids_limit') > 0) ? $this->config->get('nr_nextraids_limit') : 5;
				if($tillvalue <= $count_i){
					break;
				}
				$count_i++;
			}
		}else{
			$out .= '<span><tr><td colspan="2" class="smalltitle" align="center">'.$this->user->lang('nr_nextraids_noraids').'</td></tr>';
		}

		$out .= "</table>";
		return $out;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_nextraids_portal', nextraids_portal::__shortcuts());
?>