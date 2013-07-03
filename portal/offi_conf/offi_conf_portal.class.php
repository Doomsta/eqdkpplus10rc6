<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2012-12-16 15:31:41 +0100 (So, 16. Dez 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy_leon $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12597 $
 * 
 * $Id: offi_conf_portal.class.php 12597 2012-12-16 14:31:41Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class offi_conf_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('db', 'core', 'user', 'pdh', 'pdc', 'jquery', 'html', 'time', 'config', 'env' => 'environment');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'offi_conf';
	protected $data		= array(
		'name'			=> 'Officer-Conference',
		'version'		=> '2.1.1',
		'author'		=> 'hoofy',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Admins can post topics for officer conference.'
	);
	protected $positions= array('left1', 'left2', 'right');
	protected $settings	= array(
		'pk_oc_type' => array(
			'name' 		=> 'pk_oc_type',
			'language'	=> 'pk_oc_type',
			'property'	=> 'dropdown',
			'help'		=> '',
			'options'	=> array(
				1 => 'oc_manual',
				2 => 'oc_weekday',
				3 => 'oc_xmonthday',
			),
			'javascript'=> 'onchange="load_settings()"',
		),
		//types = 2, 3
		'pk_oc_period' => array(
			'name'		=> 'pk_oc_period',
			'language'	=> 'pk_oc_period3',
			'property'	=> 'text',
			'size'		=> 2,
			'default'	=> 1
		),
		//types = 2, 3
		'pk_oc_day'	=> array(
			'name'		=> 'pk_oc_day',
			'language'	=> 'pk_oc_day',
			'property'	=> 'dropdown',
			'help'		=> '',
			'options'	=> array(
				1 => "oc_monday",
				2 => "oc_tuesday",
				3 => "oc_wednesday",
				4 => "oc_thursday",
				5 => "oc_friday",
				6 => "oc_saturday",
				7 => "oc_sunday",
			),
		),
		//type = 1
		'pk_oc_date' => array(
			'name'		=> 'pk_oc_date',
			'language'	=> 'pk_oc_date',
			'property'	=> 'datepicker',
			'options'	=> array(
			)
		),
		// Point of time
		'pk_oc_time_type' => array(
			'name'		=> 'pk_oc_time_type',
			'language'	=> 'pk_oc_time_type',
			'property'	=> 'dropdown',
			'help'		=> '',
			'options'	=> array(
				'start' => "oc_start",
				'end'	=> "oc_end",
			),
			'default'	=> 'end',
			'javascript'=> 'onchange="load_settings()"',
		),
		'pk_oc_time'		=> array(
			'name'		=> 'pk_oc_time',
			'language'	=> 'pk_oc_end_time',
			'property'	=> 'text',
			'size'		=> 6,
			'default'	=> '20:00',
		)
	);
	protected $install	= array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'left2',
		'defaultnumber'		=> '5',
		'visibility'		=> array(2,3),
		'collapsable'		=> '1'
	);
	protected $tables	= array('module_offi_conf');
	protected $sqls		= array(
		"CREATE TABLE IF NOT EXISTS __module_offi_conf (
			`topic_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`topic_name` VARCHAR(255) NOT NULL,
			`topic_desc` TEXT DEFAULT NULL,
			`topic_time` SMALLINT(3) NOT NULL,
			`topic_expires` INT(10) NOT NULL,
			`topic_creator` INT(11) NOT NULL,
			`topic_position` SMALLINT(2) DEFAULT NULL
		) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
	");
	
	private $max_days = array(0, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	
	public function get_settings() {
		$type = $this->config->get('pk_oc_type');
		if(!$type) $type=1;
		$settings = $this->settings;
		if($type != 1) {
			unset($settings['pk_oc_date']);
			$settings['pk_oc_period']['language'] = 'pk_oc_period'.$type;
		} else {
			unset($settings['pk_oc_period']);
			unset($settings['pk_oc_day']);
		}
		$settings['pk_oc_time']['language'] = $this->config->get('pk_oc_time_type') == 'start' ? 'pk_oc_start_time' : 'pk_oc_end_time';
		return $settings;
	}

	public function output() {
		if(!$this->user->check_auth('a_', false)) {
			return '';
		}
		$out = '';
		$this->jquery->Dialog('OpenTopicWindow', $this->user->lang('oc_upd_topic'), array('url' => $this->root_path."portal/offi_conf/add_topic.php".$this->SID."&id='+topic_id+'", 'width'=>'640', 'height'=>'520', 'withid' => 'topic_id', 'onclose' => $this->env->phpself.$this->env->request_query));
		$out .= "<table width='100%' cellpadding='2' cellspacing='1' class='colorswitch'><tr><th colspan='2'>".$this->user->lang('oc_next_topics')."</th></tr>";
		$offi_conf = $this->pdc->get('portal.modul.officonf.out');
		$this->jquery->qtip('.oc_desc', 'return $(".oc_desc_c", this).html();', array('contfunc' => true));
		if(!$offi_conf) {
			$offi_conf = '';
			$sql = "SELECT topic_id, topic_name, topic_time, topic_desc, topic_creator FROM __module_offi_conf WHERE topic_expires > '".$this->db->escape($this->time->time)."' ORDER BY topic_position ASC;";
			$result = $this->db->query($sql);
			$i = 1;
			$total = 0;
			while ( $row = $this->db->fetch_record($result) ) {
				$offi_conf .= "<tr><td>".$i.". ".$row['topic_name']." ";
				$tooltip = $row['topic_desc']."<br />".$this->user->lang('oc_creator')." ".$this->pdh->get('user', 'name', array($row['topic_creator']));
				$offi_conf .= "<span class='oc_desc'><img src='{ROOT_PATH}images/global/info.png' border='0' alt='desc' /><span class='oc_desc_c' style='display:none;'>".$tooltip."</span></span>";
				$offi_conf .= " (".$row['topic_time'].$this->user->lang('oc_min').")";
				$offi_conf .= "</td><td><img onclick=\"javascript:OpenTopicWindow(".$row['topic_id'].")\" src='{ROOT_PATH}images/admin/manage_settings.png' style=\"cursor:pointer\" alt='e' />";
				$offi_conf .= "</td></tr>";
				$total += $row['topic_time'];
				$i++;
			}			
			//calc time
			list($hour,$min) = explode(':', $this->config->get('pk_oc_time'));
			$secs = 3600*$hour + 60*$min;
			if($this->config->get('pk_oc_time_type') == 'start') {
				$stime = $secs;
				$etime = $secs + 60*$total;
			} elseif($this->config->get('pk_oc_time_type') == 'end') {
				$stime = $secs - 60*$total;
				$etime = $secs;
			}
			//dont show something from the past
			$oc_date = $this->pdc->get('portal.modul.officonf.date');
			if(($oc_date + $etime) < $this->time->time) {
				$oc_date = $this->calc_date();
				$this->pdc->put('portal.modul.officonf.date', $oc_date, 7257600);
			}
			$etime += $oc_date;
			$stime += $oc_date;

			$offi_conf .= "<tr><th colspan='2'>".$this->user->lang('oc_next_oc')."</th></tr>";
			$offi_conf .= "<tr><td colspan='2'>".$this->time->user_date($stime, true).(($etime != $stime) ? " - ".$this->time->user_date($etime, false, true) : '')."</td></tr>";
			$this->pdc->put('portal.modul.officonf.out', $offi_conf);
		}
		$out .= str_replace('{ROOT_PATH}', $this->root_path, $offi_conf);
		$out .= "<tr><th colspan='2'><input type='button' name='addtopic' onclick=\"javascript:OpenTopicWindow(0)\" class='mainoption' value='".$this->user->lang('oc_add_topic')."' style=\"cursor:pointer\" /></th></tr></table>";
		return $out;
	}
	
	public function calc_date() {
		$type = $this->config->get('pk_oc_type');
		if(!$type) $type = 1;
		return call_user_func(array($this, 'calc_date'.$type));
	}
	
	private function calc_date1() {
		$date = $this->config->get('pk_oc_date');
		list($h,$m,$s) = explode('.', $this->time->date('H.i.s', $date));
		return $date-($h*3600+$m*60+$s);
	}
	
	private function calc_date2() {
		$next = $this->pdc->get('portal.modul.officonf.date');
		$N = $this->time->date('N', $next);
		if($N != $this->config->get('pk_oc_day')) {
			$N = $this->config->get('pk_oc_day') - $N;
			if($N < 0) $N += 7;
		}
		$N += 7*($this->config->get('pk_oc_period')-1);
		list($day,$mon,$yea) = explode('.', $this->time->date('d.m.Y', $next));
		$day += $N;
		if($day > $this->max_days[(int)$mon]) {
			$day -= $this->max_days[(int)$mon];
			$mon++;
			if($mon > 12) {
				$mon = 1;
				$yea++;
			}
		}
		return $this->time->mktime(0,0,0,$mon,$day,$yea);
	}
	
	private function calc_date3($monp=0) {
		$next = $this->pdc->get('portal.modul.officonf.date');
		$day = $this->config->get('pk_oc_day');
		list($dy,$mon,$yea) = explode('.', $this->time->date('d.m.Y', $next));
		$mon += $monp;
		if($mon > 12) {
			$yea++;
			$mon = 1;
		}
		$N1 = $this->time->date('N', $this->time->mktime(0,0,0,$mon,1,$yea)); //weekday of first day of month
		$N = 1;
		if($N1 != $day) {
			$N = $day - $N1 +1;
			if($N < 0) $N += 7;
		}
		$N += 7*($this->config->get('pk_oc_period')-1);
		while($N > $this->max_days[$mon]) {
			$N -= 7;
		}
		$date = $this->time->mktime(0,0,0,$mon,$N,$yea);
		return ($N > $this->time->date('d') || $monp > 0) ? $date : $this->calc_date3($monp+1);
	}
	
	public function reset() {
		$this->pdc->del_prefix('portal.modul.officonf');
		$this->config->del('pk_oc_next_oc');
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_offi_conf_portal', offi_conf_portal::__shortcuts());
?>