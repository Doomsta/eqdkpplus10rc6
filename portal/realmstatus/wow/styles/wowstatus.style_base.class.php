<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date: 2011-08-30 08:52:50 +0200 (Di, 30. Aug 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: Aderyn $
 * @copyright   2010-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 11108 $
 *
 * $Id: wowstatus.style_base.class.php 11108 2011-08-30 06:52:50Z Aderyn $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | wowstatus_style_base
  +--------------------------------------------------------------------------*/
if (!class_exists("wowstatus_style_base"))
{
  abstract class wowstatus_style_base extends gen_class
  {
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * output
     * Get the WoW Realm Status output
     *
     * @param  array  $realms  Array with Realmnames => Realmdata
     *
     * @return  string
     */
    public abstract function output($realms);

    /**
     * outputCssStyle
     * Output the CSS Style
     */
    public abstract function outputCssStyle();

  }
}

?>
