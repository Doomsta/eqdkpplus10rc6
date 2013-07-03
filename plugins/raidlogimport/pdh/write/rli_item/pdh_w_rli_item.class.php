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
 * $Id: pdh_w_rli_item.class.php 12431 2012-11-11 15:50:58Z wallenium $
 */

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 Not Found');
	exit;
}
if(!class_exists('pdh_w_rli_item')) {
class pdh_w_rli_item extends pdh_w_generic {
	public static function __shortcuts() {
		$shortcuts = array('pdh', 'db');
		return array_merge(parent::$shortcuts, $shortcuts);
	}
	
	public function add($item_id, $event_id, $itempool_id) {
		if($item_id <= 0 || $event_id <= 0 || $itempool_id <= 0) return false;
		if($this->pdh->get('rli_item', 'itempool', array($item_id, $event_id))) $this->delete($item_id, $event_id);
		if($this->db->query("INSERT INTO __raidlogimport_item2itempool :params;", array('item_id' => $item_id, 'event_id' => $event_id, 'itempool_id' => $itempool_id))) {
			$this->pdh->enqueue_hook('rli_item_update');
			return true;
		}
		return false;
	}
	
	public function delete($item_id, $event_id) {
		if($this->db->query("DELETE FROM __raidlogimport_item2itempool WHERE event_id = '".$this->db->escape($event_id)."' AND item_id = '".$this->db->escape($item_id)."';")) {
			$this->pdh->enqueue_hook('rli_item_update');
			return true;
		}
		return false;
	}
}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_rli_item', pdh_w_rli_item::__shortcuts());
?>