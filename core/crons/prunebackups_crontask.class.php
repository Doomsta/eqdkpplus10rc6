<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2011-11-01 13:38:39 +0100 (Tue, 01 Nov 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11419 $
 * 
 * $Id: prunebackups_crontask.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "prunebackups_crontask" ) ) {
	class prunebackups_crontask extends crontask {
		public static $shortcuts = array('pfh', 'time', 'tpl',
			'timekeeper'	=> 'timekeeper',
			'backup'		=> 'backup',
		);

		public $options = array(
			'days'	=> array(
				'lang'	=> 'Delete Backups older than x days',
				'name'	=> 'days',
				'type'	=> 'int',
				'size'	=> 3,
			),
			'count'	=> array(
				'lang'	=> 'Delete more than x backups',
				'name'	=> 'count',
				'type'	=> 'int',
				'size'	=> 3,
			),
		);

		public function __construct(){
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['editable']		= true;
			$this->defaults['description']	= 'Prune MySQL Backups';
		}

		public function run() {
			$crons		= $this->timekeeper->list_crons();
			$params		= $crons['prunebackups']['params'];
			$this->backup->prune_backups($params['days'], $params['count']);
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_prunebackups_crontask', prunebackups_crontask::$shortcuts);
?>