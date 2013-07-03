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
 * $Id: remove_uploadedimages_crontask.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "remove_uploadedimages_crontask" ) ) {
	class remove_uploadedimages_crontask extends crontask {
		public static $shortcuts = array('pfh');

		public function __construct(){
			$this->defaults['active']		= true;
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'weekly';
			$this->defaults['editable']		= false;
			$this->defaults['description']	= 'Delete unused uploaded images';
		}

		public function run(){
			if ( $dir = @opendir($this->pfh->FolderPath('imageupload', 'eqdkp')) ){
				while ($file = @readdir($dir)){
					if ( (is_file($this->pfh->FolderPath('imageupload', 'eqdkp') . $file)) && valid_folder($file) && (substr($file, 10, 2) == '__')){
						$this->pfh->Delete('imageupload/'.$file, 'eqdkp');
					}
				}
			}

		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_remove_uploadedimages_crontask', remove_uploadedimages_crontask::$shortcuts);
?>