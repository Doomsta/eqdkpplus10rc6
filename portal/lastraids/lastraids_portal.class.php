<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-11-10 11:52:49 +0100 (Sa, 10. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12416 $
 * 
 * $Id: lastraids_portal.class.php 12416 2012-11-10 10:52:49Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class lastraids_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'pdh', 'pdc', 'core', 'game', 'time', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'lastraids';
	protected $data		= array(
		'name'			=> 'LastRaids Module',
		'version'		=> '2.0.0',
		'author'		=> 'Corgan',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Information on last raids',
	);
	protected $positions = array('left1', 'left2', 'right');
	protected $settings	= array(
		'pk_last_raids_limit'	=> array(
			'name'		=> 'pk_last_raids_limit',
			'language'	=> 'pk_last_raids_limit',
			'property'	=> 'text',
			'size'		=> '2',
			'help'		=> 'pk_help_nextraids_limit'
		),
			'pk_set_lastraids_showloot'	=> array(
			'name'		=> 'pk_set_lastraids_showloot',
			'language'	=> 'pk_set_lastraids_showloot',
			'property'	=> 'checkbox',
			'size'		=> false,
			'options'	=> false,
			'help'		=> 'pk_help_lastitems_deactive'
		),
		'pk_lastraids_lootLimit'	=> array(
			'name'		=> 'pk_lastraids_lootLimit',
			'language'	=> 'pk_lastraids_lootLimit',
			'property'	=> 'text',
			'size'		=> '2',
			'help'		=> 'pk_help_lastitems_limit'
		),
	);
	protected $install	= array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '3',
	);
	
	protected $reset_pdh_hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update'
	);
	
	public function reset(){
		$this->pdc->del_prefix('dkp.portal.modul.lastraids.');
	}

	public function output() {
		infotooltip_js();
		$output = $this->pdc->get('dkp.portal.modul.lastraids.'.$this->root_path,false,true);
		if (!$output) {
			$output = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="colorswitch">';
			$limit = ($this->config->get('pk_last_raids_limit') > 0) ? $this->config->get('pk_last_raids_limit') : 5;
			$lastraids = $this->pdh->maget('raid', array('event', 'date', 'note', 'value'), 0, array($this->pdh->sort($this->pdh->get('raid', 'id_list'), 'raid', 'date', 'desc')));
			$lastraids = array_slice($lastraids, 0, $limit, true);
			if (!is_array($lastraids) || count($lastraids) < 1) {
				$output .= '<tr><td>'.$this->user->lang('lastraids_no_raids').'</td></tr>';
				$lastraids = array();
			}
			foreach ($lastraids as $raid_id => &$raid) {
				//Items
				$raid['items'] = '';
				if (!$this->config->get('pk_set_lastraids_showloot')) {
					$loot_limit = ($this->config->get('pk_lastraids_lootLimit') > 0) ? $this->config->get('pk_lastraids_lootLimit') : 7 ;
					$raid_items = $this->pdh->get('item', 'itemsofraid', array($raid_id));
					if (is_array($raid_items)) {
						$num = 0;
						foreach($raid_items as $item_id) {
							if($num > $loot_limit) break;
							$raid['items'] .= $this->pdh->get('item', 'link_itt', array($item_id, '{ROOT_PATH}viewitem.php', '', false, 0, 16, false, 'icon')).' ';
							$num++;
						}
					}
				}
				$img = str_replace($this->root_path, '{ROOT_PATH}', $this->game->decorate('events', array($raid['event'], 40)));
				$link = $this->pdh->get('raid', 'raidlink', array($raid_id, '{ROOT_PATH}viewraid.php'));
				$html_link = $this->pdh->geth('raid', 'raidlink', array($raid_id, '{ROOT_PATH}viewraid.php'));
				$raid['note'] = (strlen($raid['note']) > 40) ? substr($raid['note'], 0, 37).'...' : $raid['note'];
				$output .= '<tr><td width="42"><a href="'.$link.'">'.$img.'</a></td>';
				$output .= '<td>'.$html_link.'<br />'.$this->time->user_date($raid['date']).'<br />'.$raid['note'].'<br />'.$raid['items'].'</td></tr>';
			}
			$output .= '</table>';
			$this->pdc->put('dkp.portal.modul.lastraids.'.$this->root_path,$output,86400,false,true);
		}
		return str_replace('{ROOT_PATH}', $this->root_path, $output);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_lastraids_portal', lastraids_portal::__shortcuts());
?>