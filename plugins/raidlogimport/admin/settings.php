<?php
/*
* Project:     EQdkp-Plus Raidlogimport
* License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:       2008
* Date:        $Date: 2012-11-11 16:50:58 +0100 (Sun, 11 Nov 2012) $
* -----------------------------------------------------------------------
* @author      $Author: wallenium $
* @copyright   2008-2009 hoofy_leon
* @link        http://eqdkp-plus.com
* @package     raidlogimport
* @version     $Rev: 12431 $
*
* $Id: settings.php 12431 2012-11-11 15:50:58Z wallenium $
*/
define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../../../';
include_once('./../includes/common.php');

class RLI_Settings extends page_generic {
	
	private $configs = array(
			'select' 	=> array(
				'general' 		=> array('raidcount', 'raid_note_time', 'parser'),
				'member'		=> array('new_member_rank', 'member_display'), #, 'member_start_event'),
				'standby'		=> array('standby_raid')
			),
			'yes_no'	=> array(
				'general'		=> array('rli_upd_check', 'deactivate_adj', 'itempool_save', 'no_del_warn'),
				'difficulty' 	=> array('dep_match'),
				'att'		 	=> array('attendence_raid'),
				#'am'			=> array('auto_minus', 'am_value_raids', 'am_allxraids'),
				'standby'		=> array('standby_absolute', 'standby_att'),
				'member'		=> array('del_dbl_times')
			),
			'text'		=> array(
				'general'		=> array('timedkp_handle', 'bz_parse', 'loottime', 'ignore_dissed'),
				'member'		=> array('member_miss_time', 'member_raid'), #'member_start'),
				#'am'			=> array('am_raidnum', 'am_value'),
				'att'			=> array('attendence_begin', 'attendence_end', 'attendence_time', 'att_note_begin', 'att_note_end'),
				'difficulty'	=> array('diff_1', 'diff_2', 'diff_3', 'diff_4'),
				'standby'		=> array('standby_value', 'standby_raidnote')
			),
			'normal' 	=> array(
				'general'		=> array('rli_inst_version')
			),
			'ignore'	=> array(
				'ignore'		=> array('rlic_data', 'rlic_lastcheck', 'rli_inst_build')
			),
			'special'	=> array(
				'general'		=> array('3:use_dkp', '2:event_boss', '2:autocomplete'),
				'member'		=> array('3:s_member_rank'),
				'standby'		=> array('3:standby_dkptype')
			)
		);

	public static function __shortcuts() {
		$shortcuts = array('user', 'in', 'rli', 'config', 'core', 'html', 'pdh', 'pm');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$this->user->check_auth('a_raidlogimport_config');
		parent::__construct(false);
		$this->process();
	}

	public function update() {
		$messages = array();
		$bytes = array('s_member_rank', 'use_dkp', 'event_boss', 'standby_dkptype', 'autocomplete');
		$floats = array('member_start', 'attendence_begin', 'attendence_end', 'am_value');
		$copy_config = $this->rli->config();
		foreach($copy_config as $old_name => $old_value) {
			if(in_array($old_name, $bytes)) {
				$val = 0;
				if(is_array($this->in->getArray($old_name, 'int'))) {
					foreach($this->in->getArray($old_name, 'int') as $pos) {
						$val += $pos;
					}
				}
				$data[$old_name] = $val;
			} elseif(in_array($old_name, $floats)) {
				$data[$old_name] = number_format($this->in->get($old_name), 2, '.', '');
			} else {
				$data[$old_name] = $this->in->get($old_name, '');
			}
			if(isset($data[$old_name]) AND $data[$old_name] != $old_value) { //Update
				$this->config->set($old_name, $data[$old_name], 'raidlogimport');
				$this->rli->reload_config();
				$messages[] = $old_name;
			}
		}
		$this->display(array(implode(', ', $messages)));
	}

	public function display($messages=array()) {
		if($messages) {
			$this->rli->__construct();
			foreach($messages as $name) {
				$this->core->message($name, $this->user->lang('bz_save_suc'), 'green');
			}
		}
		//select ranks
		$new_member_rank = $this->pdh->aget('rank', 'name', 0, array($this->pdh->get('rank', 'id_list')));

		//select parsers
		$parser = array(
			'eqdkp' => $this->user->lang('parser_eqdkp'),
			'plus' => $this->user->lang('parser_plus'),
			'magicdkp' => $this->user->lang('parser_magicdkp'),
			'empty' => $this->user->lang('parser_empty')
		);

		//select raidcount
		$raidcount = array();
		for($i=0; $i<=3; $i++) {
			$raidcount[$i] = $this->user->lang('raidcount_'.$i);
		}

		//select null_sum & standbyraidoptions
		$standby_raid = array();
		for($i=0; $i<=2; $i++) {
			$standby_raid[$i] = $this->user->lang('standby_raid_'.$i);
		}

		//select member_start_event
		$member_start_event = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));

		//select member_display
		$member_display = array(0 => $this->user->lang('member_display_0'), 1 => $this->user->lang('member_display_1'), 2 => $this->user->lang('member_display_2'));

		//select raid_note_time
		$raid_note_time = array(0 => $this->user->lang('raid_note_time_0'), 1 => $this->user->lang('raid_note_time_1'));

		$k = 2;
		$holder = array();
		foreach($this->configs as $display_type => $hold) {
			foreach($hold as $holde => $names) {
				foreach($names as $name) {
					switch($display_type) {
						case 'select':
							$holder[$holde][$k]['value'] = $this->html->DropDown($name, $$name, $this->rli->config($name));
							$holder[$holde][$k]['name'] = $name;
							break;

						case 'yes_no':
							$a = $k;
							if($name == 'rli_upd_check') {
								$k = 1;
							}
							$check_1 = '';
							$check_0 = '';
							if($this->rli->config($name)) {
								$check_1 = "checked='checked'";
							} else {
								$check_0 = "checked='checked'";
							}
							$holder[$holde][$k]['value'] = "<input type='radio' name='".$name."' value='1' ".$check_1." />".$this->user->lang('yes')."&nbsp;&nbsp;&nbsp;";
							$holder[$holde][$k]['value'] .= "&nbsp;&nbsp;&nbsp;<input type='radio' name='".$name."' value='0' ".$check_0." />".$this->user->lang('no');
							$holder[$holde][$k]['name'] = $name;
							$k = $a;
							break;

						case 'normal':
							$a = $k;
							if($name == 'rli_inst_version') {
								$k = 0;
								$holder[$holde][$k]['value'] = $this->pm->get_data('raidlogimport', 'version');
							} else {
								$holder[$holde][$k]['value'] = $this->rli->config($name);
							}
							$holder[$holde][$k]['name'] = $name;
							$k = $a;
							break;

						case 'text':
							$holder[$holde][$k]['value'] = "<input type='text' name='".$name."' value='".$this->rli->config($name)."' class='maininput' />";
							$holder[$holde][$k]['name'] = $name;
							break;

						case 'special':
							list($num_of_opt, $name) = explode(':', $name);
							$value = $this->rli->config($name);
							$pv = array(0,1,2,4,8,16,32);
							$holder[$holde][$k]['value'] = '';
							for($i=1; $i<=$num_of_opt; $i++) {
								$checked = ($value & $pv[$i]) ? 'checked="checked"' : '';
								$holder[$holde][$k]['value'] .= "<span class='nowrap'><input type='checkbox' name='".$name."[]' value='".$pv[$i]."' ".$checked." />".$this->user->lang($name.'_'.$pv[$i])."</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							}
							$holder[$holde][$k]['name'] = $name;
							break;

						default:
							//do nothing
							break;
					}
					$k++;
				}
			}
		}
		$num = 1;
		foreach($holder as $type => $hold) {
			ksort($hold);
			if($type == 'difficulty' AND $this->config->get('default_game') != 'wow') {
				continue;
			}
			$this->tpl->assign_block_vars('holder', array(
				'TITLE'	=> $this->user->lang('title_'.$type),
				'NUM'	=> $num)
			);
			$num++;
			foreach($hold as $nava) {
				$add = ($this->user->lang($nava['name'].'_help', false, false)) ? $this->user->lang($nava['name'].'_help') : '';
				if($nava['name'] == 'member_display') {
					if(extension_loaded('gd')) {
						$info = gd_info();
						$add = sprintf($add, '<span class=\'positive\'>'.$info['GD Version'].'</span>');
					} else {
						$add = sprintf($add, $this->user->lang('no_gd_lib'));
					}
				}
				if($add != '') {
					$add = $this->html->ToolTip($add, '<img alt="help" src="'.$this->root_path.'images/global/info.png" />');
				}
				if($this->user->lang($nava['name'].'_warn', false, false)) {
					$warn = $this->user->lang($nava['name'].'_warn');
				} else {
					$warn = '';
				}
				if($warn != '') {
					$warn = $this->html->ToolTip($warn, '<img width="16" height="16" alt="help" src="'.$this->root_path.'images/global/false.png" />');
				}
				$this->tpl->assign_block_vars('holder.config', array(
					'NAME'	=> $this->user->lang($nava['name']).' '.$add.' '.$warn,
					'VALUE' => $nava['value'])
				);
			}
		}
		$this->tpl->assign_vars(array(
			'L_CONFIG' => $this->user->lang('raidlogimport').' '.$this->user->lang('settings'),
			'L_SAVE'	 => $this->user->lang('bz_save'),
			'L_MANUAL'	=> $this->user->lang('rli_manual'),
			#'S_GERMAN'	=> ($this->user->lang_name == 'german') ? true : false,
			'TAB_JS'	=> $this->jquery->Tab_header('rli_config'))
		);

		$this->core->set_vars(array(
			'page_title' 		=> sprintf($this->user->lang('admin_title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '.$this->user->lang('configuration'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'settings.html',
			'display'           => true,
			)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_RLI_Settings', RLI_Settings::__shortcuts());
registry::register('RLI_Settings');
?>