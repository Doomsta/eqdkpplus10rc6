<?php
 /*
 * Project:     EQdkp-Plus Raidlogimport
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date: 2013-02-07 15:23:51 +0100 (Thu, 07 Feb 2013) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2008-2009 hoofy_leon
 * @link        http://eqdkp-plus.com
 * @package     raidlogimport
 * @version     $Rev: 13027 $
 *
 * $Id: pdh_w_rli_boss.class.php 13027 2013-02-07 14:23:51Z hoofy_leon $
 */

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 Not Found');
	exit;
}
if(!class_exists('pdh_w_rli_boss')) {
class pdh_w_rli_boss extends pdh_w_generic {
	public static function __shortcuts() {
		$shortcuts = array('pdh', 'config', 'db');
		return array_merge(parent::$shortcuts, $shortcuts);
	}
	
	public function add($string, $note, $bonus=0.0, $timebonus=0.0, $diff=0, $tozone=0, $sort=0) {
		if($this->db->query("INSERT INTO __raidlogimport_boss :params;", array(
						'boss_string'	=> $string,
						'boss_note'		=> $note,
						'boss_bonus'	=> $bonus,
						'boss_timebonus'=> $timebonus,
						'boss_diff'		=> $diff,
						'boss_tozone'	=> $tozone,
						'boss_sort'		=> $sort,
						'boss_active'	=> '1'))) {
			$id = $this->db->insert_id();
			$this->pdh->enqueue_hook('rli_boss_update', array($id));
			$log_action = array(
				'{L_ID}'			=> $id,
				'{L_BZ_TYPE}'   	=> '{L_BZ_BOSS_S}',
				'{L_BZ_STRING}'		=> $string,
				'{L_BZ_BNOTE}'		=> $note,
				'{L_BZ_BONUS}'		=> $bonus,
				'{L_BZ_TIMEBONUS}'	=> $timebonus,
				'{L_BZ_TOZONE}' 	=> ($tozone) ? $this->pdh->get('rli_zone', 'note', array($tozone)) : '{L_BZ_NO_ZONE}',
				'{L_BZ_DIFF}' 		=> $diff
			);
			$this->log_insert('action_raidlogimport_bz_add', $log_action, true, 'raidlogimport');
			return $id;
		}
		return false;
	}
	
	public function update($id, $string='', $note='', $bonus=false, $timebonus=false, $diff=false, $tozone=false, $sort=false) {
		if(!$id) {
			return false;
		}
		$old = array(
			'string'	=> implode($this->config->get('bz_parse', 'raidlogimport'), $this->pdh->get('rli_boss', 'string', array($id))),
			'note'		=> $this->pdh->get('rli_boss', 'note', array($id)),
			'bonus'		=> $this->pdh->get('rli_boss', 'bonus', array($id)),
			'timebonus'	=> $this->pdh->get('rli_boss', 'timebonus', array($id)),
			'diff'		=> $this->pdh->get('rli_boss', 'diff', array($id)),
			'tozone'	=> $this->pdh->get('rli_boss', 'tozone', array($id)),
			'sort'		=> $this->pdh->get('rli_boss', 'sort', array($id))
		);
		$data = array(
			'boss_string'	=> ($string == '') ? $this->pdh->get('rli_boss', 'string', array($id)) : $string,
			'boss_note'		=> ($note == '') ? $this->pdh->get('rli_boss', 'note', array($id)) : $note,
			'boss_bonus'	=> ($bonus === false) ? $this->pdh->get('rli_boss', 'bonus', array($id)) : $bonus,
			'boss_timebonus'=> ($timebonus === false) ? $this->pdh->get('rli_boss', 'timebonus', array($id)) : $timebonus,
			'boss_diff'		=> ($diff === false) ? $this->pdh->get('rli_boss', 'diff', array($id)) : $diff,
			'boss_tozone'	=> ($tozone === false) ? $this->pdh->get('rli_boss', 'tozone', array($id)) : $tozone,
			'boss_sort'		=> ($sort === false) ? $this->pdh->get('rli_boss', 'sort', array($id)) : $sort
		);
		if($this->changed($old, $data)) {
			if($this->db->query("UPDATE __raidlogimport_boss SET :params WHERE boss_id = ?;", $data, $id)) {
				$this->pdh->enqueue_hook('rli_boss_update', array($id));
				$log_action = array(
					'{L_ID}'			=> $id,
					'{L_BZ_TYPE}'   	=> '{L_BZ_ZONE_S}',
					'{L_BZ_STRING}'		=> $old['string']." => ".$string,
					'{L_BZ_BNOTE}'		=> $old['note']." => ".$note,
					'{L_BZ_BONUS}'		=> $old['bonus']." => ".$bonus,
					'{L_BZ_TIMEBONUS}'	=> $old['timebonus']." => ".$timebonus,
					'{L_BZ_DIFF}' 		=> $old['diff']." => ".$diff,
					'{L_BZ_TOZONE}' 	=> (($old['tozone']) ? $this->pdh->get('rli_zone', 'note', array($old['tozone'])) : '{L_BZ_NO_ZONE}').(($tozone) ? $this->pdh->get('rli_zone', 'note', array($tozone)) : '{L_BZ_NO_ZONE}'),
				);
				$this->log_insert('action_raidlogimport_bz_upd', $log_action, true, 'raidlogimport');
				return $id;
			}
		} else {
			return $id;
		}
		return false;
	}
	
	public function del($id) {
		if(!$id) {
			return false;
		}
		$old = array(
			'string'	=> implode(', ', $this->pdh->get('rli_boss', 'string', array($id))),
			'note'		=> $this->pdh->get('rli_boss', 'note', array($id)),
			'bonus'		=> $this->pdh->get('rli_boss', 'bonus', array($id)),
			'timebonus'	=> $this->pdh->get('rli_boss', 'timebonus', array($id)),
			'diff'		=> $this->pdh->get('rli_boss', 'diff', array($id)),
			'tozone'	=> $this->pdh->get('rli_boss', 'tozone', array($id)),
			'sort'		=> $this->pdh->get('rli_boss', 'sort', array($id))
		);
		if($this->db->query("DELETE FROM __raidlogimport_boss WHERE boss_id = ?;", false, $id)) {
			$this->pdh->enqueue_hook('rli_boss_update', array($id));
			$log_action = array(
				'{L_ID}'			=> $id,
				'{L_BZ_TYPE}'   	=> '{L_BZ_ZONE_S}',
				'{L_BZ_STRING}'		=> $old['string'],
				'{L_BZ_BNOTE}'		=> $old['note'],
				'{L_BZ_BONUS}'		=> $old['bonus'],
				'{L_BZ_TIMEBONUS}'	=> $old['timebonus'],
				'{L_BZ_DIFF}' 		=> $old['diff'],
				'{L_BZ_TOZONE}' 	=> ($old['tozone']) ? $this->pdh->geth('rli_zone', 'event', array($old['tozone'], false)) : '{L_BZ_NO_ZONE}',
			);
			$this->log_insert('action_raidlogimport_bz_del', $log_action, true, 'raidlogimport' );
			return $id;
		}
		return false;
	}
	
	public function set_active($boss_id, $active) {
		$active = ($active) ? '1' : '0';
		if($this->db->query("UPDATE __raidlogimport_boss SET boss_active = '".$this->db->escape($active)."' WHERE boss_id = ?;", false, $boss_id)) {
			$this->pdh->enqueue_hook('rli_boss_update', array($boss_id));
			return true;
		}
		return false;
	}
	
	private function changed($array1, $array2) {
		foreach($array1 as $val) {
			if(!in_array($val, $array2, true)) {
				return true;
			}
		}
		return false;
	}
}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_rli_boss', pdh_w_rli_boss::__shortcuts());
?>