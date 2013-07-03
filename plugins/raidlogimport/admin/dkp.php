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
* $Id: dkp.php 12431 2012-11-11 15:50:58Z wallenium $
*/

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../';
include_once('./../includes/common.php');

class rli_import extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'rli', 'in', 'tpl', 'core', 'pm', 'config', 'jquery',
			'adj'		=> 'rli_adjustment',
			'item'		=> 'rli_item',
			'member'	=> 'rli_member',
			'parser'	=> 'rli_parse',
			'raid'		=> 'rli_raid',
		);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$this->user->check_auth('a_raidlogimport_dkp');
		
		$handler = array(
			'checkraid'	=> array('process' => 'process_raids'),
			'checkmem'	=> array('process' => 'process_members'),
			'checkitem'	=> array('process' => 'process_items'),
			'save_itempools' => array('process' => 'itempool_save'),
			'checkadj'	=> array('process' => 'process_adjustments'),
#			'viewall'	=> array('process' => 'process_views'),
			'insert'	=> array('process' => 'insert_log')
		);
		parent::__construct(false, $handler);
		// save template state to return to if errors occur
		$this->tpl->save_state('rli_start');
		$this->process();
	}
	
	private function process_error($main_process) {
		if($this->rli->error_check()) {
			$error = $this->rli->get_error();
			if(!empty($error['process']) && $error['process'] != $main_process) {
				$this->tpl->load_state('rli_start');
			}
			if(is_array($error['messages'])) {
				$this->core->messages($error['messages']);
			}
			if(!empty($error['process']) && $error['process'] != $main_process) {
				$this->$error['process'](false);
			}
		}
	}

	public function process_raids($error_out=true) {
		if($this->in->get('checkraid') == $this->user->lang('rli_send')) {
			$this->rli->flush_cache();
			if($this->in->exists('log') && $this->rli->config('parser') != 'empty') {
				$log = trim(str_replace("&", "and", stripslashes(html_entity_decode($_POST['log']))));
				$log = (is_utf8($log)) ? $log : utf8_encode($log);
				$log = simplexml_load_string($log);
				if ($log === false) {
					message_die($this->user->lang('xml_error'));
				} else {
					$this->parser->parse_string($log);
				}
			}
			$this->rli->add_cache_data('progress', 'members');
		}
		$this->raid->add_new($this->in->get('raid_add', 0));
		if($this->in->get('checkraid') == $this->user->lang('rli_calc_note_value')) {
			$this->raid->recalc();
		}

		$this->raid->display(true);

		$this->tpl->assign_vars(array(
			'USE_TIMEDKP' => ($this->rli->config('use_dkp') & 2),
			'USE_BOSSDKP' => ($this->rli->config('use_dkp') & 1))
		);
		//language
		lang2tpl();
		
		// error processing
		if($error_out) $this->process_error('process_raids');
		$this->rli->nav(0);

		$this->core->set_vars(array(
			'page_title'        => sprintf($this->user->lang('admin_title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '.$this->user->lang('rli_check_data'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'raids.html',
			'display'           => true)
		);
	}

	public function process_members($error_out=true) {
		$this->member->add_new($this->in->get('members_add', 0));

		//display members
		$this->member->display(true);

		// show raids
		$this->raid->display();

		//language
		lang2tpl();
		
		// error processing
		if($error_out) $this->process_error('process_members');
		$this->rli->nav(1);

		$this->tpl->assign_vars(array(
			'S_ATT_BEGIN'	 => ($this->rli->config('attendence_begin') > 0 AND !$this->rli->config('attendence_raid')) ? TRUE : FALSE,
			'S_ATT_END'		 => ($this->rli->config('attendence_end') > 0 AND !$this->rli->config('attendence_raid')) ? TRUE : FALSE,
			'MEMBER_DISPLAY' => ($this->rli->config('member_display') == 1) ? $this->raid->th_raidlist : false,
			'RAIDCOUNT'		 => ($this->rli->config('member_display') == 1) ? $this->raid->count() : 1,
			'RAIDCOUNT3'	 => ($this->rli->config('member_display') == 1) ? $this->raid->count()+2 : 3,
			'DETAIL_RAIDLIST' =>($this->rli->config('member_display') == 2 && extension_loaded('gd')) ? true : false)
		);

		$this->core->set_vars(array(
			'page_title'        => sprintf($this->user->lang('admin_title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '.$this->user->lang('rli_check_data'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'members.html',
			'display'           => true)
		);
	}

	public function process_items($error_out=true) {
		$this->item->add_new($this->in->get('items_add', 0));
		$this->member->display();
		$this->raid->display();
		$this->item->display(true);
		
		$this->tpl->assign_vars(array(
			'S_ATT_BEGIN'	=> ($this->rli->config('attendence_begin') > 0 AND !$this->rli->config('attendence_raid')) ? true : false,
			'S_ATT_END'		=> ($this->rli->config('attendence_end') > 0 AND !$this->rli->config('attendence_raid')) ? true : false)
		);

		//language
		lang2tpl();
		
		// error processing
		if($error_out) $this->process_error('process_items');
		$this->rli->nav(2);
		
		$this->core->set_vars(array(
			'page_title'        => sprintf($this->user->lang('admin_title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '.$this->user->lang('rli_check_data'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'items.html',
			'display'           => true)
		);
	}
	
	public function itempool_save() {
		$this->item->save_itempools();
		$this->process_items();
	}

	public function process_adjustments($error_out=true) {
		$this->adj->add_new($this->in->get('adjs_add', 0));

		$this->member->display();
		$this->raid->display();
		$this->item->display();
		$this->adj->display(true);

		$this->tpl->assign_vars(array(
			'S_ATT_BEGIN'	=> ($this->rli->config('attendence_begin') > 0 AND !$this->rli->config('attendence_raid')) ? true : false,
			'S_ATT_END'		=> ($this->rli->config('attendence_end') > 0 AND !$this->rli->config('attendence_raid')) ? true : false)
		);

		//language
		lang2tpl();
		
		// error processing
		if($error_out) $this->process_error('process_adjustments');
		$this->rli->nav(3);
		
		$this->core->set_vars(array(
			'page_title'        => sprintf($this->user->lang('admin_title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '.$this->user->lang('rli_check_data'),
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'adjustments.html',
			'display'           => true)
		);
	}

	public function insert_log() {
		global $db, $core, $user, $tpl, $pm, $rli, $pdh;
		
		$message = array();
		$bools = $this->rli->check_data();
		if(!in_array('miss', $bools) AND !in_array(false, $bools)) {
			#$this->db->query("START TRANSACTION");
			$this->member->insert();
			$this->raid->insert();
			$this->item->insert();
			if(!$this->rli->config('deactivate_adj')) $this->adj->insert();
			$this->process_error('insert_log');
			$this->rli->process_pdh_queue();
			$this->pdh->process_hook_queue();
			$this->rli->flush_cache();
			$message[] = $this->user->lang('bz_save_suc');
			foreach($message as $answer) {
				$this->tpl->assign_block_vars('sucs', array(
					'PART1'	=> $answer)
				);
			}
			$this->tpl->assign_vars(array(
				'L_SUCCESS' => $this->user->lang('rli_success'),
				'L_LINKS'	=> $this->user->lang('links'))
			);
	
			$this->core->set_vars(array(
				'page_title'        => sprintf($this->user->lang('admin_title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '.$this->user->lang('rli_imp_suc'),
				'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
				'template_file'     => 'success.html',
				'display'           => true)
			);
		} else {
			unset($_POST);
			$check = $this->user->lang('rli_missing_values').'<br />';
			foreach($bools['false'] as $loc => $la) {
				if($la == 'miss') {
					$check .= $this->user->lang('rli_'.$loc.'_needed');
				}
				$check .= '<input type="submit" name="check'.$loc.'" value="'.$this->user->lang('rli_check'.$loc).'" class="mainoption" /><br />';
			}
			$this->tpl->assign_vars(array(
				'L_NO_IMP_SUC'	=> $this->user->lang('rli_imp_no_suc'),
				'CHECK'			=> $check)
			);
			$this->rli->nav(4);
			$this->core->set_vars(array(
				'page_title'		=> sprintf($this->user->lang('admin_title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '.$this->user->lang('rli_imp_no_suc'),
				'template_path'		=> $this->pm->get_data('raidlogimport', 'template_path'),
				'template_file'		=> 'check_input.html',
				'display'			=> true,
				)
			);
		}
	}

	public function display($messages=array()) {
		if($messages) {
			foreach($messages as $title => $message) {
				$type = ($title == 'rli_error' or $title == 'rli_no_mem_create') ? 'red' : 'green';
				if(is_array($message)) {
					$message = implode(',<br />', $message);
				}
				$this->core->message($message, $this->user->lang($title).':', $type);
			}
		}
		$this->tpl->assign_vars(array(
			'L_DATA_SOURCE'	 => $this->user->lang('rli_data_source'),
			'L_CONTINUE_OLD' => $this->user->lang('rli_continue_old'),
			'L_INSERT'		 => $this->user->lang('rli_dkp_insert'),
			'L_SEND'		 => $this->user->lang('rli_send'),
			'DISABLED'		 => ($this->rli->data_available()) ? '' : 'disabled="disabled"',
			'S_STEP1'        => true)
		);
		
		$this->tpl->add_js("\$('#show_log_form').click(function() {\$('#log_form').show(200)});",'docready');

		$this->core->set_vars(array(
			'page_title'        => sprintf($this->user->lang('admin_title_prefix'), $this->config->get('guildtag'), $this->config->get('dkp_name')).': '."DKP String",
			'template_path'     => $this->pm->get_data('raidlogimport', 'template_path'),
			'template_file'     => 'log_insert.html',
			'display'           => true,
			)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_rli_import', rli_import::__shortcuts());
registry::register('rli_import');
?>