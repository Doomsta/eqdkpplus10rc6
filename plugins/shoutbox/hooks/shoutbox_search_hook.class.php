<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-11-11 18:36:16 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 12434 $
 *
 * $Id: shoutbox_search_hook.class.php 12434 2012-11-11 17:36:16Z godmod $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | shoutbox_search_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('shoutbox_search_hook'))
{
  class shoutbox_search_hook extends gen_class
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
		  'shoutbox' => array(
			'category'    => $this->user->lang('shoutbox'),
			'module'      => 'shoutbox',
			'method'      => 'search',
			'permissions' => array('u_shoutbox_view'),
		  ),
		);

		return $search;
	}
  }
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	registry::add_const('short_shoutbox_search_hook', shoutbox_search_hook::$shortcuts);
}
?>