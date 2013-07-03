<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date: 2012-11-29 19:27:13 +0100 (Thu, 29 Nov 2012) $
* -----------------------------------------------------------------------
* @author		$Author: godmod $
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev: 12518 $
*
* $Id: manage_calendars.php 12518 2012-11-29 18:27:13Z godmod $
*/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path.'common.php');

class Manage_Calendars extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'html');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_calendars_man');
		$handler = array(
			'save' => array('process' => 'save', 'csrf'=>true),
		);
		parent::__construct(false, $handler, array('calendars', 'name'), null, 'calendar_ids[]');
		$this->process();
	}

	public function save() {
		$noranks = false;
		$retu = array();
		$calendars = $this->get_post();
		if($calendars) {
			$id_list = $this->pdh->get('calendars', 'idlist');
			foreach($calendars as $calendar) {
				$func = (in_array($calendar['id'], $id_list)) ? 'update_calendar' : 'add_calendar';
				$retu[] = $this->pdh->put('calendars', $func, array($calendar['id'], $calendar['name'], $calendar['color'], $calendar['feed'], $calendar['private'], $calendar['type']));
				$names[] = $calendar['name'];
			}
			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('save_nosuc'), 'text' => implode(', ', $names), 'color' => 'red');
			} elseif(in_array(true, $retu)) {
				$message = array('title' => $this->user->lang('save_suc'), 'text' => implode(', ', $names), 'color' => 'green');
			}
		}else{
			$message = array('title' => '', 'text' => $this->user->lang('no_calendars_selected'), 'color' => 'grey');
		}
		$this->display($message);
	}

	public function delete() {
		$noranks = false;
		$calendar_ids = $this->in->getArray('calendar_ids', 'int');
		if($calendar_ids) {
			foreach($calendar_ids as $id) {
				$names[] = $this->pdh->get('calendars', 'name', ($id));
				$retu[] = $this->pdh->put('calendars', 'delete_calendar', array($id));
			}
			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('del_no_suc'), 'text' => implode(', ', $names), 'color' => 'red');
			} else {
				$message = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $names), 'color' => 'green');
			}
		} else {
			$message = array('title' => '', 'text' => $this->user->lang('no_calendars_selected'), 'color' => 'grey');
		}
		$this->display($message);
	}

	public function display($messages=false) {
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		$types = array(
			1	=> $this->user->lang(array('calendars_types', 1)),
			2	=> $this->user->lang(array('calendars_types', 2)),
			3	=> $this->user->lang(array('calendars_types', 3)),
		);

		$new_id = 0;
		$order = $this->in->get('order','0.0');
		$ranks = $this->pdh->aget('calendars', 'name', 0, array($this->pdh->get('calendars', 'idlist')));
		unset($ranks[0]);
		if($order == '0.0') {
			arsort($ranks);
		} else {
			asort($ranks);
		}
		$key = 0;
		$new_id = 1;
		ksort($ranks);
		foreach($ranks as $id => $name) {
			$this->tpl->assign_block_vars('calendars', array(
				'KEY'		=> $key,
				'DELETABLE'	=> $this->pdh->get('calendars', 'is_deletable', array($id)),
				'ID'		=> $id,
				'NAME'		=> $name,
				'TYPE'		=> $this->html->DropDown('calendars['.$key.'][type]', $types, $this->pdh->get('calendars', 'type', array($id)), '', '', 'input', 'calendars'.$key),
				'COLOR'		=> $this->jquery->colorpicker('cal_'.$key, $this->pdh->get('calendars', 'color', array($id)), 'calendars['.$key.'][color]'),
				'PRIVATE'	=> $this->pdh->get('calendars', 'private', array($id)),
				'FEED'		=> $this->pdh->get('calendars', 'feed', array($id)),
			));
			$key++;
			$new_id = ($new_id == $id) ? $id+1 : $new_id;
		}
		$this->confirm_delete($this->user->lang('confirm_delete_calendars'));

		$this->tpl->assign_vars(array(
			'SID'		=> $this->SID,
			'ID'		=> $new_id,
			'KEY'		=> $key,
			'TYPE'		=> $this->html->DropDown('calendars['.$key.'][type]', $types, '', '', '', 'input', 'calendars'.$key),
			'COLOR'		=> $this->jquery->colorpicker('cal_'.$key, '', 'calendars['.$key.'][color]'),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_calendars'),
			'template_file'		=> 'admin/manage_calendars.html',
			'display'			=> true)
		);
	}

	private function get_post() {
		$calendars = array();
		$selected = $this->in->getArray('calendar_ids', 'int');
		if($this->in->exists('calendars', 'string')) {
			foreach($this->in->getArray('calendars', 'string') as $key => $calendar) {
				if(isset($calendar['id']) && $calendar['id'] && !empty($calendar['name'])) {
					$calendars[] = array(
						'selected'	=> (in_array($calendar['id'], $selected)) ? $calendar['id'] : false,
						'id'		=> $this->in->get('calendars:'.$key.':id',0),
						'name'		=> $this->in->get('calendars:'.$key.':name',''),
						'feed'		=> $this->in->get('calendars:'.$key.':feed','', 'raw'),
						'color'		=> $this->in->get('calendars:'.$key.':color',''),
						'private'	=> $this->in->get('calendars:'.$key.':suffix',0),
						'type'		=> $this->in->get('calendars:'.$key.':type',0)
					);
				}
			}
			return $calendars;
		}
		return false;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Calendars', Manage_Calendars::__shortcuts());
registry::register('Manage_Calendars');
?>