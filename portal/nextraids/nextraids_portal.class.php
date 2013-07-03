<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-10-20 12:58:19 +0200 (Sa, 20. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12309 $
 *
 * $Id: nextraids_portal.class.php 12309 2012-10-20 10:58:19Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class nextraids_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'pdh', 'core', 'time', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'nextraids';
	protected $data		= array(
		'name'			=> 'Nextraids',
		'version'		=> '3.0.2',
		'author'		=> 'WalleniuM',
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
			foreach($caleventids as $eventid){
				$eventextension	= $this->pdh->get('calendar_events', 'extension', array($eventid));
				$raidclosed		= ($this->pdh->get('calendar_events', 'raidstatus', array($eventid)) == '1') ? true : false;
				if($eventextension['calendarmode'] != 'raid'){
					continue;
				}

				// switch closed raids if enabled
				if($this->config->get('nr_nextraids_hideclosed') && $raidclosed){
					continue;
				}

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
				$out .= '<tr class="row1">
							<td colspan="2">
								<span style="float:left;font-weight:bold;">
									'.$this->time->user_date($this->pdh->get('calendar_events', 'time_start', array($eventid))).', '.$this->time->user_date($this->pdh->get('calendar_events', 'time_start', array($eventid)), false, true).' - '.$this->time->user_date($this->pdh->get('calendar_events', 'time_end', array($eventid)), false, true).'
								</span>
								<span style="float: right;width: 24px;">
									'.$signinstatus.'
								</span>
							</td>
						</tr>
						<tr class="row2">
							<td valign="middle" align="center" width="44">
							<a href="'.$raidplink.'">'.$this->pdh->get('event', 'html_icon', array($eventextension['raid_eventid'], 40)).'</a>
							</td>
							<td>';
				if($raidclosed){
					$out .= '<div style="text-decoration: line-through;">'.$this->pdh->get('event', 'name', array($eventextension['raid_eventid'])).' ('.$eventextension['attendee_count'].') </div>';
				}else{
					$out .= '<a href="'.$raidplink.'">'.$this->pdh->get('event', 'name', array($eventextension['raid_eventid'])).' ('.$eventextension['attendee_count'].') </a><br/>';
				}

				if (is_array($counts)){
					foreach($counts as $countid=>$countdata){
						$out .= '<span class="status'.$countid.'">'.$raidstatus[$countid].': '.$countdata.'</span><br/>';
					}
				}
				$out .= "</td></tr>";

				// end the foreach if x raids are reached
				$tillvalue = ($this->config->get('nr_nextraids_limit') > 0) ? $this->config->get('nr_nextraids_limit') : 5;
				if($tillvalue <= $count_i){
					break;
				}
				$count_i++;
			}
		}else{
			$out .= '<tr><td colspan="2" class="smalltitle" align="center">'.$this->user->lang('nr_nextraids_noraids').'</td></tr>';
		}

		$out .= "</table>" ;
		return $out;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_nextraids_portal', nextraids_portal::__shortcuts());
?>