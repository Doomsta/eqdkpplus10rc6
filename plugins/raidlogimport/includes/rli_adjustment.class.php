<?php
 /*
 * Project:     EQdkp-Plus Raidlogimport
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date: 2012-11-11 16:50:58 +0100 (Sun, 11 Nov 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2008-2009 hoofy_leon
 * @link        http://eqdkp-plus.com
 * @package     raidlogimport
 * @version     $Rev: 12431 $
 *
 * $Id: rli_adjustment.class.php 12431 2012-11-11 15:50:58Z wallenium $
 */

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 Not Found');
	exit;
}

if(!class_exists('rli_adjustment')) {
  class rli_adjustment extends gen_class {
	public static $shortcuts = array('rli', 'in', 'pdh', 'user', 'tpl', 'html',
		'member'	=> 'rli_member',
		'raid'		=> 'rli_raid',
	);
	public static $dependencies = array('rli');

	public function __construct() {
		$this->adjs = $this->rli->get_cache_data('adj');
		if($this->in->exists('adjs')) $this->load_adjs();
	}

	private function config($name) {
		return $this->rli->config($name);
	}
	
	public function reset() {
		$this->adjs = array();
	}

	public function add($reason, $member, $value, $event, $date=0, $raid=0) {
		$this->adjs[] = array('reason' => $reason, 'member' => $member, 'value' => runden($value), 'date' => $date, 'raid' => $raid);
	}

	public function add_new($num) {
		while($num > 0) {
			$this->adjs[] = array('reason' => '');
			$num--;
		}
	}
	
	public function update($key, $values) {
		if(is_array($values)) {
			foreach($values as $type => $data) {
				$this->adjs[$key][$type] = $data;
			}
			return true;
		}
		return false;
	}
	
	public function load_adjs() {
		$this->adjs = array();
		foreach($_POST['adjs'] as $a => $adj) {
			if(!(isset($adj['delete']) AND $adj['delete'])) {
				$this->adjs[$a] = $this->in->getArray('adjs:'.$a, '');
				$this->adjs[$a]['value'] = runden($this->in->floatvalue($adj['value']));
			}
		}
	}
	
	public function display($with_form=false) {
		if($this->rli->get_cache_data('progress') == 'adjustments') $this->rli->add_cache_data('progress', 'finish');
		$members = $this->member->get_for_dropdown(4);
		$events = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
		$raid_select = array_merge(array($this->user->lang('none')), $this->raid->raidlist());
		$a = 0;
		if(is_array($this->adjs)) {
			foreach($this->adjs as $a => $adj) {
				$ev_sel = (isset($adj['event'])) ? $adj['event'] : 0;
				if(runden($adj['value']) == '0' || runden($adj['value']) == '-0') {
					unset($data['adjs'][$a]);
					continue;
				}
				$this->tpl->assign_block_vars('adjs', array(
					'MEMBER'	=> $this->html->widget(array('type' => 'dropdown', 'name' => 'adjs['.$a.'][member]', 'options' => $members, 'selected' => $adj['member'], 'id' => 'adjs_'.$a.'_member', 'no_lang' => true)),
					'EVENT'		=> $this->html->widget(array('type' => 'dropdown', 'name' => 'adjs['.$a.'][event]', 'options' => $events, 'selected' => $ev_sel, 'id' => 'adjs_'.$a.'_event', 'no_lang' => true)),
					'NOTE'		=> $adj['reason'],
					'VALUE'		=> $adj['value'],
					'RAID'		=> $this->html->widget(array('type' => 'dropdown', 'name' => 'adjs['.$a.'][raid]', 'options' => $raid_select, 'selected' => $adj['raid'], 'id' => 'adjs_'.$a.'raid', 'no_lang' => true)),
					'KEY'		=> $a,
				));
			}
		}
		$this->tpl->assign_block_vars('adjs', array(
			'KEY'		=> 999,
			'MEMBER'	=> $this->html->widget(array('type' => 'dropdown', 'name' => 'adjs[999][member]', 'options' => $members, 'selected' => 0, 'id' => 'adjs_999_member', 'no_lang' => true)),
			'EVENT'		=> $this->html->widget(array('type' => 'dropdown', 'name' => 'adjs[999][event]', 'options' => $events, 'selected' => 0, 'id' => 'adjs_999_event', 'no_lang' => true)),
			'RAID'		=> $this->html->widget(array('type' => 'dropdown', 'name' => 'adjs[999][raid]', 'options' => $raid_select, 'selected' => 0, 'id' => 'adjs_999_raid', 'no_lang' => true)),
			'DISPLAY'	=> 'style="display: none;"',
			'DELCHK'	=> 'checked="checked"',
		));
		$this->tpl->add_js(
"$('#rli_select_all').click(function() {
	if($('.rli_select_me').prop('checked')) {
		$('.rli_select_me').removeAttr('checked');
	} else {
		$('.rli_select_me').attr('checked', 'checked');
	}
});
var rli_key = ".($a+1).";
$('#add_adj_button').click(function() {
	var adj = $('#adj_999').clone(true);
	adj.find('.rli_select_me').removeAttr('checked');
	adj.html(adj.html().replace(/999/g, rli_key));
	adj.attr('id', 'adj_'+rli_key);
	adj.removeAttr('style');
	$('#adj_999').before(adj);
	rli_key++;
});", 'docready');
	}
	
	public function check_adj_exists($member, $reason, $raid_id=0) {
		if(is_array($this->adjs)) {
			foreach($this->adjs as $key => $adj) {
				if($adj['member'] == $member AND $adj['reason'] == $reason AND (!$raid_id OR $adj['raid'] == $raid_id)) {
					return $key;
				}
			}
		}
		return false;
	}

	public function check($bools) {
		if(is_array($this->adjs)) {
			foreach($this->adjs as $key => $adj) {
				if(!$adj['event'] OR !$adj['member'] OR !$adj['reason'] OR !$adj['value']) {
					$bools['false']['adj'] = false;
				}
			}
		} else {
			$bools['false']['adj'] = 'miss';
		}
		return $bools;
	}
	
	//TODO: try and group adjustments
	public function insert() {
		foreach($this->adjs as $key => $adj) {
			$this->rli->pdh_queue('adjustments', $key, 'adjustment', 'add_adjustment', array($adj['value'], $adj['reason'], array($this->member->name_ids[$adj['member']]), $adj['event'], $adj['raid'], $adj['time']), array('param' => 4, 'type' => 'raids'));
		}
		return true;
	}
	
	public function __destruct() {
		$this->rli->add_cache_data('adj', $this->adjs);
		parent::__destruct();
	}
  }
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	registry::add_const('short_rli_adjustment', rli_adjustment::$shortcuts);
	registry::add_const('dep_rli_adjustment', rli_adjustment::$dependencies);
}
?>