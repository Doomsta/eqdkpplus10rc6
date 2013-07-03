<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-11-24 18:48:21 +0100 (Sa, 24. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy_leon $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12504 $
 * 
 * $Id: add_topic.php 12504 2012-11-24 17:48:21Z hoofy_leon $
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../../';
include_once($eqdkp_root_path.'common.php');

class addtopic extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user','tpl', 'in', 'db', 'core', 'time', 'pdc', 'config', 'portal');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$this->user->check_auth('a_');
		parent::__construct(false, false, array(), null, '', 'id');
		$this->process();
	}
	
	private function expires() {
		$oc_date = $this->portal->get_module('offi_conf')->calc_date();
		list($h, $min, $s) = explode(':', $this->time->date('H:i:s', $oc_date));
		// find 5 o'clock in the morning
		$secs = $mins = $hs = 0;
		if($s != 0) {
			$secs = 60 - $s;
			$min++;
		}
		if($min != 0) {
			$mins = 60 - $min;
			$h++;
		}
		if($h >= 5) {
			$hs = 24 - $h + 5;
		} else {
			$hs = 5 - $h;
		}
		$add = $secs + 60*$mins + 3600*$hs;
		// minimum time 2 hours
		if($add < 7200) $add += 7200;
		return $oc_date + $add;
	}

	public function update() {
		$params = array(
			'topic_name' => $this->in->get('t_name', $this->user->lang('oc_no_name')),
			'topic_desc' => $this->in->get('t_desc', $this->user->lang('oc_no_desc')),
			'topic_position' => $this->in->get('t_posi', 9),
			'topic_time' => $this->in->get('t_time', 15),
			'topic_expires' => $this->expires()
		);
		if($this->url_id) {
			$sql = "UPDATE __module_offi_conf SET :params WHERE topic_id = ?;";
		} else {
			$sql = "INSERT INTO __module_offi_conf :params;";
			$params['topic_creator'] = $this->user->id;
		}
		$this->finish($this->db->query($sql, $params, $this->url_id), 'save');
	}

	public function delete() {
		$sql = "DELETE FROM __module_offi_conf WHERE (topic_id = '".$this->db->escape($this->url_id)."' OR topic_expires < '".$this->db->escape($this->time->time)."');";
		$this->finish($this->db->query($sql), 'delete');
	}

	public function finish($boolsql, $type) {
		if($boolsql) {
			$this->core->message('', $this->user->lang('oc_'.$type.'_success'), 'green', true, false);
			$this->pdc->del_suffix('officonf');
		} else {
			$this->core->message('', $this->user->lang('oc_'.$type.'_no_success'), 'red', true, false);
		}
		$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
		$this->core->set_vars(array(
			'page_title'		=> sprintf($this->user->lang('title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '.$this->user->lang('user_list'),
			'template_path'		=> 'portal/offi_conf/templates/',
			'template_file'		=> 'add_topic.html',
			'header_format'		=> 'simple',
			'display'			=> true)
		);
	}

	public function display() {
		$data = array();
		if($this->url_id != 0) {
			$sql = "SELECT topic_name, topic_desc, topic_time, topic_position FROM __module_offi_conf WHERE topic_id = '".$this->db->escape($this->url_id)."';";
			$result = $this->db->query($sql);
			$data = $this->db->fetch_row($result);
			$this->db->free_result($result);
			$this->tpl->assign_vars(array(
				'TOPIC_NAME'	=> $data['topic_name'],
				'TOPIC_TIME'	=> $data['topic_time'],
				'TOPIC_POSI'	=> $data['topic_position'],
				'TOPIC_DESC'	=> $data['topic_desc'],
				'TOPIC_ID'		=> $this->url_id,
				'DELETE'		=> true
			));
		}
		$this->core->set_vars(array(
			'page_title'		=> sprintf($this->user->lang('title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '.$this->user->lang('user_list'),
			'template_path'		=> 'portal/offi_conf/templates/',
			'template_file'		=> 'add_topic.html',
			'header_format'		=> 'simple',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_addtopic', addtopic::__shortcuts());
registry::register('addtopic');
?>