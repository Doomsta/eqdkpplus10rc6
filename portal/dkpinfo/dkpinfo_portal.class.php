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
 * $Id: dkpinfo_portal.class.php 12416 2012-11-10 10:52:49Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class dkpinfo_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'pdh', 'pdc');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'dkpinfo';
	protected $data		= array(
		'name'			=> 'DKPinfo Module',
		'version'		=> '2.0.0',
		'author'		=> 'EQdkp-Plus Team',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'DKP Overview',
	);
	protected $positions = array('left1', 'left2', 'right');
	protected $settings	= array();
	protected $install	= array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'left2',
		'defaultnumber'		=> '0',
	);
	
	protected $reset_pdh_hooks = array(
			'item_update',
			'member_update',
			'raid_update'
	);
	
	public function reset(){
		$this->pdc->del('dkp.portal.modul.dkpinfo');
	}

	public function output() {
  		$output = $this->pdc->get('dkp.portal.modul.dkpinfo',false,true);
  		if (!$output) {
			$output = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="noborder">
						<tr><td class="row1">'.$this->user->lang('portal_info_raids').'</td><td class="row1">'.count($this->pdh->get('raid', 'id_list')).'</td></tr>
						<tr><td class="row2">'.$this->user->lang('portal_info_player').'</td><td class="row2">'.count($this->pdh->get('member', 'id_list')).'</td></tr>
						<tr><td class="row1">'.$this->user->lang('portal_info_items').'</td><td class="row1">'.count($this->pdh->get('item', 'id_list')).'</td></tr>
						</table>
						';
			$this->pdc->put('dkp.portal.modul.dkpinfo',$output,86400,false,true);
		}
		return $output;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_dkpinfo_portal', dkpinfo_portal::__shortcuts());
?>