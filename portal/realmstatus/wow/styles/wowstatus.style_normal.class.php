<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date: 2012-06-05 17:53:50 +0200 (Di, 05. Jun 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: shoorty $
 * @copyright   2010-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 11807 $
 *
 * $Id: wowstatus.style_normal.class.php 11807 2012-06-05 15:53:50Z shoorty $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');
  exit;
}


if (!class_exists('wowstatus_style_base'))
{
  include_once(registry::get_const('root_path').'portal/realmstatus/wow/styles/wowstatus.style_base.class.php');
}

/*+----------------------------------------------------------------------------
  | wowstatus_style_normal
  +--------------------------------------------------------------------------*/
if (!class_exists("wowstatus_style_normal"))
{
  class wowstatus_style_normal extends wowstatus_style_base
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('user', 'env' => 'environment', 'tpl');
      return array_merge(parent::$shortcuts, $shortcuts);
    }

    /* Base image path */
    private $image_path;

    /**
     * Constructor
     */
    public function __construct()
    {
      // call base constructor
      parent::__construct();

      // set image path
      $this->image_path = $this->env->link.'portal/realmstatus/wow/images/normal/';
    }

    /**
     * output
     * Get the WoW Realm Status output
     *
     * @param  array  $realms  Array with Realmnames => Realmdata
     *
     * @return  string
     */
    public function output($realms)
    {
      // set output
      $output = '';

      // process all realms
      if (is_array($realms))
      {
        foreach ($realms as $realmname => $realmdata)
        {
          // set "tr" div
          $output .= '<div class="tr">';

          // output status
          switch ($realmdata['status'])
          {
            case 'up':
              $output .= '<div class="td"><img src="'.$this->image_path.'up.png" alt="Online" title="'.$realmname.'" /></div>';
              break;
            case 'down':
              $output .= '<div class="td"><img src="'.$this->image_path.'down.png" alt="Offline" title="'.$realmname.'" /></div>';
              break;
            default:
              $output .= '<div class="td"><img src="'.$this->image_path.'down.png" alt="Offline" title="'.$realmname.' ('.$this->user->lang('rs_unknown').')" /></div>';
              break;
          }

          // output realm name
          $output .= '<div class="td">'.$realmname.'</div>';

          // output server type
          switch ($realmdata['type'])
          {
            case 'pvp':
              $output .= '<div class="td rs_wow_pvp">PvP</div>';
              break;
            case 'rppvp':
              $output .= '<div class="td rs_wow_rppvp">RP-PvP</div>';
              break;
            case 'rp':
              $output .= '<div class="td rs_wow_rp">RP</div>';
              break;
            case 'pve':
              $output .= '<div class="td rs_wow_pve">PvE</div>';
              break;
            default:
              $output .= '<div class="td">'.$this->user->lang('rs_unknown').'</div>';
              break;
          }

          // close "tr" div
          $output .= '</div>';
        }
      }

      return $output;
    }

    /**
     * outputCssStyle
     * Output the CSS Style
     */
    public function outputCssStyle()
    {
      $style = '.rs_wow_pve, .rs_wow_rppvp {
                  color: #EBDBA2;
                }

                .rs_wow_pvp {
                  color: #CC3333;
                }

                .rs_wow_rp {
                  color: #33CC33;
                }';

      // add css
      $this->tpl->add_css($style);
    }

  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_wowstatus_style_normal', wowstatus_style_normal::__shortcuts());
?>
