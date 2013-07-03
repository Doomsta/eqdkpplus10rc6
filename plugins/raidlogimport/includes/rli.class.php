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
* $Id: rli.class.php 12431 2012-11-11 15:50:58Z wallenium $
*/

if(!defined('EQDKP_INC'))
{
	header('HTTP/1.0 Not Found');
	exit;
}

class rli extends gen_class {
	public static $shortcuts = array('cconfig' => 'config', 'game', 'db', 'user', 'jquery', 'tpl', 'pdh',
		'adj'		=> 'rli_adjustment',
		'item'		=> 'rli_item',
		'member'	=> 'rli_member',
		'raid'		=> 'rli_raid',
	);
	public static $dependencies = array('db', 'cconfig' => 'config');

	private $bonus = array();
	private $config = array();
	private $bk_list = array();
	private $events = array();
	private $member_ranks = array();
	private $data = array();
	private $errors = array();
	private $process_order = array('process_raids', 'process_members', 'process_items', 'process_adjustments', 'process_views');
	
	private $pdh_queue = array();
	
	private $destruct_called = false;

	public function __construct() {
		$this->config = $this->cconfig->get('raidlogimport');
		if(empty($this->config['bz_parse'])) {
			$this->config['bz_parse'] = ',';
			$this->cconfig->set('bz_parse', ',', 'raidlogimport');
		}
		// load cache_data
		$this->get_cache_data('');
	}
	
	public function reload_config() {
		$this->config = $this->cconfig->get('raidlogimport');
	}

	public function config($name='') {
		return ($name == '') ? $this->config : ((isset($this->config[$name])) ? $this->config[$name] : null);
	}

	public function suffix($string, $append, $diff) {
		if($this->game->get_game() == 'wow' AND $append) {
			return $string.$this->config('diff_'.$diff);
		}
		return $string;
	}
	
	public function error_check() {
		return count($this->errors);
	}
	
	public function error($process, $message) {
		$this->errors[] = array('process' => $process, 'message' => array('title' => $this->user->lang('error'), 'text' => $message, 'color' => 'red'));
	}
	
	public function get_error() {
		$data = array();
		foreach($this->process_order as $process) {
			foreach($this->errors as $error) {
				if($process == $error['process']) {
					$data['process'] = $process;
					break 2;
				}
			}
		}
		foreach($this->errors as $error) {
			$data['messages'][] = $error['message'];
		}
		return $data;
	}

	public function get_cache_data($type) {
		if(!$this->data) {
			$sql = "SELECT cache_class, cache_data FROM __raidlogimport_cache;";
			$result = $this->db->query($sql);
			while ( $row = $this->db->fetch_record($result) ) {
				$this->data[$row['cache_class']] = ($this->cconfig->get('enable_gzip')) ? unserialize(gzuncompress($row['cache_data'])) : unserialize($row['cache_data']);
			}
			$this->data['fetched'] = true;
		}
		return (isset($this->data[$type])) ? $this->data[$type] : null;
	}
	
	public function nav($selection) {
		$this->jquery->Tab_header('rli_nav_tabs');
		$this->jquery->Tab_Select('rli_nav_tabs', $selection);
		
		if($this->config('deactivate_adj')) {
			$ids = array('rli_nav_raids', 'rli_nav_members', 'rli_nav_items', 'rli_nav_finish');
			$progress = array('members' => 1, 'items' => 2, 'finish' => 3);
		} else {
			$ids = array('rli_nav_raids', 'rli_nav_members', 'rli_nav_items', 'rli_nav_adjustments', 'rli_nav_finish');
			$progress = array('members' => 1, 'items' => 2, 'adjustments' => 3, 'finish' => 4);
		}
		$position = $progress[$this->data['progress']];
		for($i=0;$i<=$position;$i++) {
			$this->tpl->add_js('$("#'.$ids[$i].'").click(function(){
	$("#'.$ids[$i].' ~ input").removeAttr("disabled");
	$("#rli_import_form").submit();
});','docready');
		}
		if($position+1 < count($ids)) {
			$str = $position+1;
			for($i=$position+2;$i<count($ids);$i++) {
				$str .= ', '.$i;
			}
			$this->tpl->add_js('$("#rli_nav_tabs").tabs(\'option\', \'disabled\', ['.$str.']);', 'docready');
		}
	}
	
	public function data_available() {
		return !empty($this->data);
	}

	public function check_data() {
		$bools = array();
		$bools = $this->raid->check($bools);
		$bools = $this->member->check($bools);
		$bools = $this->item->check($bools);
		$bools = $this->adj->check($bools);
		return $bools;
	}
	
	public function pdh_queue($type, $key, $module, $tag, $params, $id_insert=false) {
		$this->pdh_queue[] = array('type' => $type, 'key' => $key, 'module' => $module, 'tag' => $tag, 'params' => $params, 'id_insert' => $id_insert);
	}
	
	public function process_pdh_queue() {
		foreach($this->pdh_queue as $data) {
			if($data['id_insert']) $data['params'][$data['id_insert']['param']] = ${$data['id_insert']['type']}[$data['params'][$data['id_insert']['param']]];
			/* foreach($data['id_insert'] as $id_insert) {
				if(is_array($data['params'][$id_insert['param']])) {
					foreach($data['params'][$id_insert['param']] as &$val) {
						$val = ${$id_insert['type']}[$val];
					}
				} else {
					
				}
			} */
			${$data['type']}[$data['key']] = $this->pdh->put($data['module'], $data['tag'], $data['params']);
		}
	}
	
	public function add_cache_data($type, $data) {
		$this->data[$type] = $data;
	}
	
	public function flush_cache() {
		$this->raid->reset();
		$this->member->reset();
		$this->item->reset();
		$this->adj->reset();
		return $this->db->query("TRUNCATE __raidlogimport_cache;");
	}

	public function __destruct() {
		$this->db->query("TRUNCATE __raidlogimport_cache;");
		$sql = "INSERT INTO __raidlogimport_cache
				(cache_class, cache_data)
				VALUES ";
		if($this->data) {
			foreach($this->data as $type => $data) {
				$data = ($this->cconfig->get('enable_gzip')) ? gzcompress(serialize($data)) : serialize($data);
				$sqls[] = "('".$type."', '".$this->db->escape($data)."')";
			}
			$sql .= implode(", ", $sqls).";";
			$this->db->query($sql);
		}
		parent::__destruct();
	}
}//class

if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	registry::add_const('short_rli', rli::$shortcuts);
	registry::add_const('dep_rli', rli::$dependencies);
}
?>