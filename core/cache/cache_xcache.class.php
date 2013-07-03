<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date: 2012-08-31 21:14:54 +0200 (Fri, 31 Aug 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12011 $
 * 
 * $Id: cache_xcache.class.php 12011 2012-08-31 19:14:54Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !interface_exists( "plus_datacache" ) ) {
	require_once($eqdkp_root_path . 'core/cache/cache.iface.php');
}

if ( !class_exists( "cache_xcache" ) ) {
	class cache_xcache extends gen_class implements plus_datacache{

		public function put( $key, $data, $ttl, $global_prefix, $compress = false ) {
			$key = $global_prefix.$key;
			$ret = xcache_set($key, $data, $ttl);
			return $ret;
		}

		public function get( $key, $global_prefix, $uncompress = false ) {
			$key = $global_prefix.$key;
			return xcache_get($key);
		}

		public function del( $key, $global_prefix ) {
			$key = $global_prefix.$key;
			xcache_unset($key);
			return true;
		}
	}//end class
}//end if
?>