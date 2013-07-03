<?php
/*
* Project:     EQdkp-Plus Raidlogimport
* License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:       2008
* Date:        $Date: 2013-02-07 15:23:51 +0100 (Thu, 07 Feb 2013) $
* -----------------------------------------------------------------------
* @author      $Author: hoofy_leon $
* @copyright   2008-2009 hoofy_leon
* @link        http://eqdkp-plus.com
* @package     raidlogimport
* @version     $Rev: 13027 $
*
* $Id: raidlogimport_plugin_class.php 13027 2013-02-07 14:23:51Z hoofy_leon $
*/

if ( !defined('EQDKP_INC') ) {
	die('You cannot access this file directly.');
}

class raidlogimport extends plugin_generic {
	public static function __shortcuts() {
		$shortcuts = array('core', 'user', 'db', 'pdh', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $vstatus = 'Beta';
	public $version = '0.6.1.2';
	
	public function pre_install() {
		//initialize config
		$this->config->set($this->create_default_configs(), '', $this->get_data('code'));
		$sqls = $this->create_install_sqls();
		foreach($sqls as $sql) {
			$this->add_sql(SQL_INSTALL, $sql);
		}
	}
	
	public function pre_uninstall() {
		$this->config->del(array_keys($this->create_default_configs()), $this->get_data('code'));
		$sqls = $this->create_uninstall_sqls();
		foreach($sqls as $sql) {
			$this->add_sql(SQL_UNINSTALL, $sql);
		}
	}

	public function __construct() {
		parent::__construct();
		//Load Game-Specific Language
		$lang_file = $this->root_path.'plugins/raidlogimport/language/'.$this->user->lang_name.'/'.$this->config->get('default_game').'_lang.php';
		if(file_exists($lang_file)) {
			include($lang_file);
			$this->user->add_lang($this->user->lang_name, $lang);
		}

		$this->add_dependency(array(
			'plus_version' => '0.7',
			'games'	=> array('wow', 'eq', 'rom'))
		);

		$this->add_data(array(
			'name'				=> 'Raid-Log-Import',
			'code'				=> 'raidlogimport',
			'path'				=> 'raidlogimport',
			'contact'			=> 'bloodyhoof@gmx.net',
			'template_path' 	=> 'plugins/raidlogimport/templates/',
			'version'			=> $this->version,
			'author'			=> 'Hoofy',
			'description'		=> $this->user->lang('raidlogimport_short_desc'),
			'long_description'	=> $this->user->lang('raidlogimport_long_desc'),
			'homepage'			=> EQDKP_PROJECT_URL,
			'manuallink'		=> ($this->user->lang_name != 'german') ? false : $this->root_path . 'plugins/raidlogimport/language/'.$this->user->lang_name.'/Manual.pdf',
			'icon'				=> $this->root_path.'plugins/raidlogimport/images/report.png',
			)
		);

		//permissions
		$this->add_permission('a', 'config', 'N', $this->user->lang('configuration'), array(2,3));
		$this->add_permission('a', 'dkp', 'N', $this->user->lang('raidlogimport_dkp'), array(2,3));
		$this->add_permission('a', 'bz', 'N', $this->user->lang('raidlogimport_bz'), array(2,3));
		
		//pdh-modules
		$this->add_pdh_read_module('rli_zone');
		$this->add_pdh_read_module('rli_boss');
		$this->add_pdh_read_module('rli_item');
		$this->add_pdh_write_module('rli_zone');
		$this->add_pdh_write_module('rli_boss');
		$this->add_pdh_write_module('rli_item');

		//menu
		$this->add_menu('admin_menu', $this->gen_admin_menu());
	}
	
	private function create_default_configs() {
		//create config-data
		$config_data = array(
			'new_member_rank' 	=> '1',
			'raidcount'			=> '0', //0 = one raid, 1 = raid per hour, 2 = raid per boss, 3 = raid per hour and per boss
			'loottime'			=> '600', //time after bosskill to assign loot to boss (in seconds)
			'attendence_begin' 	=> '0',
			'attendence_end'	=> '0',
			'attendence_raid'	=> '0', //create extra raid for attendence?
			'attendence_time'	=> '900', //time of inv (in seconds)
			'event_boss'		=> '0',  //exists an event per boss?
			'adj_parse'			=> ': ', //string, which separates the reason and the value for a adjustment in the note of a member
			'bz_parse'			=> ',',  //separator, which is used for separating the different strings of a boss or zone
			'parser'			=> 'plus',  //which format has the xml-string?
			'rli_upd_check'		=> '1',		//enable update check?
			'use_dkp'			=> '1',		//1: bossdkp, 2:zeitdkp, 4: event-dkp
			'deactivate_adj'	=> '0',
			'ignore_dissed'		=> '',		//ignore disenchanted and bank loot?
			'member_miss_time' 	=> '300',	//time in secs member can miss without it being tracked
			's_member_rank'		=> '0',		//show member_rank? (0: no, 1: memberpage, 2: lootpage, 4: adjustmentpage, 3:member+lootpage, 5:adjustments+memberpage, 6: loot+adjustmentpage, 7: overall)
			'att_note_begin'	=> $this->user->lang('rli_att').' '.$this->user->lang('rli_start'),	//note for attendence_start-raid
			'att_note_end'		=> $this->user->lang('rli_att').' '.$this->user->lang('rli_end'),	//  "	"		"	 _end-raid
			'raid_note_time'	=> '0', 	//0: exact time (20:03:43-21:03:43); 1: hour (1. hour, 2. hour)
			'timedkp_handle'	=> '0',		//should timedkp be given exactly(0) or fully after x minutes
			'member_display'	=> '2',		//0: multi-dropdown; 1: checkboxes; 2: detailed join/leave
			'standby_raid'		=> '0',		//0: no extra-raid for standby, 1: extra-raid, 2: attendance on normal raid
			'standby_absolute'	=> '0',		//0: relative dkp, 1: absolute dkp
			'standby_value'		=> '0',		//value in percent or absolute
			'standby_att'		=> '0', 	//shall standbys get att start/end?
			'standby_dkptype'	=> '0',		//which dkp shall standbys get? (1 boss, 2 time, 4 event)
			'standby_raidnote'	=> $this->user->lang('standby_raid_note'),		//note for standby-raid
			'member_raid'		=> '50',	//percent which member has to be in raid, to gain assignment to raid
			'itempool_save'		=> '1',		//save itempool per item & event
			'del_dbl_times'		=> '0',		//delete double leave/joins
			'autocomplete'		=> '0',		//auto-complete fields (1 member, 2 items)
		);
		if(strtolower($this->config->get('default_game')) == 'wow') {
			$config_data = array_merge($config_data, array(
				'diff_1'	=> ' (10)',		//suffix for 10-player normal
				'diff_2'	=> ' (25)', 	//suffix for 25-player normal
				'diff_3'	=> ' HM (10)',	//suffix for 10-player heroic
				'diff_4'	=> ' HM (25)',	//suffix for 25-player heroic
				'dep_match'	=> '1'			//also append suffix to boss-note?
			));
		}
		return $config_data;
	}

	private function create_install_sqls() {
		$install_sqls = array(
			"CREATE TABLE IF NOT EXISTS __raidlogimport_boss (
				`boss_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`boss_string` VARCHAR(255) NOT NULL,
				`boss_note` VARCHAR(255) NOT NULL,
				`boss_bonus` FLOAT(5,2) NOT NULL DEFAULT 0,
				`boss_timebonus` FLOAT(5,2) NOT NULL DEFAULT 0,
				`boss_diff` INT NOT NULL DEFAULT 0,
				`boss_tozone` INT NOT NULL DEFAULT 0,
				`boss_sort` INT NOT NULL DEFAULT 0,
				`boss_active` INT(1) NOT NULL DEFAULT 1
			) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			"CREATE TABLE IF NOT EXISTS __raidlogimport_zone (
				`zone_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`zone_string` VARCHAR(255) NOT NULL,
				`zone_event` INT NOT NULL,
				`zone_timebonus` FLOAT(5,2) NOT NULL DEFAULT 0,
				`zone_diff` INT NOT NULL DEFAULT 0,
				`zone_sort` INT NOT NULL DEFAULT 0,
				`zone_active` INT(1) NOT NULL DEFAULT 1
			) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			"CREATE TABLE IF NOT EXISTS __raidlogimport_cache (
				`cache_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`cache_class` VARCHAR(255) NOT NULL,
				`cache_data` BLOB DEFAULT NULL
			) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
			"CREATE TABLE IF NOT EXISTS __raidlogimport_item2itempool (
				`item_id` INT NOT NULL,
				`event_id` INT NOT NULL,
				`itempool_id` INT NOT NULL,
				PRIMARY KEY (`item_id`, `event_id`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
		
		//add default bz_data
		$file = $this->root_path.'plugins/raidlogimport/games/'.$this->config->get('default_game').'/bz_sql.php';
		if(is_file($file)) {
			include_once($file);
			$data = (!empty(${$this->user->lang_name})) ? ${$this->user->lang_name} : $english;
			if (is_array($data)) {
				$zones = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
				foreach($data['zone'] as $bz) {
					$id = 1;
					foreach($zones as $zid => $zone) {
						if(strpos($zone, $bz[1]) !== false) {
							$id = $zid;
							break;
						}
					}							
					$install_sqls[] = 	"INSERT INTO __raidlogimport_zone
											(zone_string, zone_event, zone_timebonus, zone_diff, zone_sort)
										VALUES
											('".$this->db->escape($bz[0])."', '".$id."', '".$bz[2]."', '".$bz[3]."', '".$bz[4]."');";
				}
				foreach($data['boss'] as $bz) {
					$install_sqls[] = 	"INSERT INTO __raidlogimport_boss
											(boss_string, boss_note, boss_bonus, boss_timebonus, boss_diff, boss_tozone, boss_sort)
										VALUES
											('".$this->db->escape($bz[0])."', '".$this->db->escape($bz[1])."', '".$bz[2]."', '".$bz[3]."', '".$bz[4]."', '".$bz[5]."', '".$bz[6]."');";
				}
			}
		}
		return $install_sqls;
	}
	
	private function create_uninstall_sqls() {
		$uninstall_sqls = array(
			"DROP TABLE IF EXISTS __raidlogimport_boss;",
			"DROP TABLE IF EXISTS __raidlogimport_zone;",
			"DROP TABLE IF EXISTS __raidlogimport_item2itempool;",
			"DROP TABLE IF EXISTS __raidlogimport_cache;");
		return $uninstall_sqls;
	}
	
	public function gen_admin_menu() {
		return array(array(
			'icon' => './../../plugins/raidlogimport/images/report.png',
			'name' => $this->user->lang('raidlogimport'),
			1 => array(
				'link' => 'plugins/' . $this->code . '/admin/settings.php'.$this->SID,
				'text' => $this->user->lang('settings'),
				'check' => 'a_raidlogimport_config',
				'icon' => 'manage_settings.png'),
			2 => array(
				'link' => 'plugins/' . $this->code . '/admin/bz.php'.$this->SID,
				'text' => $this->user->lang('raidlogimport_bz'),
				'check' => 'a_raidlogimport_bz',
				'icon' => './../../plugins/raidlogimport/images/report_edit.png'),
			3 => array(
				'link' => 'plugins/' . $this->code . '/admin/dkp.php'.$this->SID,
				'text' => $this->user->lang('raidlogimport_dkp'),
				'check' => 'a_raidlogimport_dkp',
				'icon' => './../../plugins/raidlogimport/images/report_add.png')
		));
	}

	public function get_info($varname) {
		return $this->$varname;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_raidlogimport', raidlogimport::__shortcuts());
?>