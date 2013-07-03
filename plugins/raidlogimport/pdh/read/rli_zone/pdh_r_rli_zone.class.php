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
 * $Id: pdh_r_rli_zone.class.php 12431 2012-11-11 15:50:58Z wallenium $
 */

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 Not Found');
	exit;
}
if(!class_exists('pdh_r_rli_zone')) {
class pdh_r_rli_zone extends pdh_r_generic {
	public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'config', 'user', 'game', 'pdh');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	private $data = array();
	public $hooks = array('rli_zone_update');
	
	public function init() {
		$this->data = $this->pdc->get('pdh_rli_zone');
		if(!$this->data) {
			$sql = "SELECT zone_id, zone_string, zone_event, zone_timebonus, zone_diff, zone_sort, zone_active FROM __raidlogimport_zone;";
			if($result = $this->db->query($sql)) {
				while($row = $this->db->fetch_record($result)) {
					$this->data[$row['zone_id']]['string'] = explode($this->config->get('bz_parse', 'raidlogimport'), $row['zone_string']);
					$this->data[$row['zone_id']]['event'] = $row['zone_event'];
					$this->data[$row['zone_id']]['timebonus'] = $row['zone_timebonus'];
					$this->data[$row['zone_id']]['diff'] = $row['zone_diff'];
					$this->data[$row['zone_id']]['sort'] = $row['zone_sort'];
					$this->data[$row['zone_id']]['active'] = ($row['zone_active']) ? 1 : 0;
				}
			} else {
				$this->data = array();
				return false;
			}
			$this->db->free_result($result);
			$this->pdc->put('pdh_rli_zone', $this->data, null);
		}
		return true;
	}
	
	public function reset() {
		unset($this->data);
		$this->pdc->del('pdh_rli_zone');
	}
	
	public function get_id_list($active_only=true) {
		if($active_only) {
			$out = array();
			foreach($this->data as $id => $data) {
				if($data['active']) $out[] = $id;
			}
			return $out;
		}
		return array_keys($this->data);
	}
	
	public function get_id_string($string, $diff) {
		foreach($this->data as $id => $data) {
			if(in_array($string, $data['string']) AND ($diff == 0 OR $data['diff'] == 0 OR $diff == $data['diff'])) {
				return $id;
			}
		}
		return false;
	}
	
	public function get_string($id) {
		return $this->data[$id]['string'];
	}
	
	public function get_html_string($id) {
		return implode(', ', $this->data[$id]['string']).$this->get_html_diff($id);
	}
	
	public function get_event($id) {
		return $this->data[$id]['event'];
	}
	
	public function get_html_event($id, $with_icon=true) {
		$icon = ($with_icon) ? $this->game->decorate('events', array($this->get_event($id))) : '';
		return $icon.$this->pdh->get('event', 'name', array($this->get_event($id)));
	}
	
	public function get_eventbystring($string) {
		foreach($this->data as $id => $data) {
			if(in_array($string, $data['string'])) {
				return $this->get_event($id);
			}
		}
		return false;
	}
	
	public function get_timebonus($id) {
		return $this->data[$id]['timebonus'];
	}
	
	public function get_diff($id) {
		return $this->data[$id]['diff'];
	}
	
	public function get_html_diff($id) {
		return ($this->get_diff($id)) ? ' &nbsp; ('.$this->user->lang('diff_'.$this->get_diff($id)).')' : '';
	}
	
	public function get_sort($id) {
		return $this->data[$id]['sort'];
	}
	
	public function get_active($id) {
		return $this->data[$id]['active'];
	}
}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_rli_zone', pdh_r_rli_zone::__shortcuts());
?>