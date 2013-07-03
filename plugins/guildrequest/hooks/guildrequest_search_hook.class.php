<?php
/*
 * Project:     EQdkp guildrequest
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-11-11 18:36:16 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest
 * @version     $Rev: 12434 $
 *
 * $Id: guildrequest_search_hook.class.php 12434 2012-11-11 17:36:16Z godmod $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | guildrequest_search_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('guildrequest_search_hook'))
{
  class guildrequest_search_hook extends gen_class
  {
    /* List of dependencies */
    public static $shortcuts = array('user');

	/**
    * hook_search
    * Do the hook 'search'
    *
    * @return array
    */
	public function search()
	{
		// build search array
		$search = array(
		  'guildrequest' => array(
			'category'    => $this->user->lang('guildrequest'),
			'module'      => 'guildrequest_requests',
			'method'      => 'search',
			'permissions' => array('u_guildrequest_view'),
		  ),
		);

		return $search;
	}
  }
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	registry::add_const('short_guildrequest_search_hook', guildrequest_search_hook::$shortcuts);
}
?>