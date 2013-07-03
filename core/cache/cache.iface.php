<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date: 2011-08-07 20:17:16 +0200 (Sun, 07 Aug 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 10914 $
 * 
 * $Id: cache.iface.php 10914 2011-08-07 18:17:16Z wallenium $
 */

if( !defined( 'EQDKP_INC' ) ) {
	die( 'Do not access this file directly.' );
}

if( !interface_exists( "plus_datacache" ) ) {
	interface plus_datacache {
		public function put( $key, $data, $ttl, $global_prefix, $compress = false );
		public function get( $key, $global_prefix, $uncompress = false );
		public function del( $key, $global_prefix );
	}//end interface
}
//end if
?>