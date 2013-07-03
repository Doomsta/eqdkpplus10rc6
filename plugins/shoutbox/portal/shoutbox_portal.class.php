<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-11-01 13:38:39 +0100 (Di, 01. Nov 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11419 $
 *
 * $Id: shoutbox_portal.class.php 11419 2011-11-01 12:38:39Z hoofy $
 */

if (!defined('EQDKP_INC'))
{
    header('HTTP/1.0 404 Not Found'); exit;
}

/*+----------------------------------------------------------------------------
  | shoutbox_portal
  +--------------------------------------------------------------------------*/
class shoutbox_portal extends portal_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('pm', 'user', 'pdh');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  /**
   * Portal path
   */
  protected $path = 'shoutbox';
  /**
   * Portal data
   */
  protected $data = array(
    'name'        => 'Shoutbox Module',
    'version'     => '0.3.3',
    'author'      => 'Aderyn',
    'contact'     => 'Aderyn@gmx.net',
    'description' => 'Display a shoutbox',
  );
  /**
   * Positions this Module may appear
   */
  protected $positions = array('left1', 'left2', 'right', 'middle', 'bottom');
  /**
   * Settings
   */
  protected $settings = array(
    'pk_shoutbox_output_count_limit'  => array(
      'name'      => 'sb_output_count_limit',
      'language'  => 'sb_output_count_limit',
      'property'  => 'text',
      'size'      => '3',
    ),
    'pk_shoutbox_show_date'           => array(
        'name'      => 'sb_show_date',
        'language'  => 'sb_show_date',
        'property'  => 'checkbox',
    ),
    'pk_shoutbox_show_archive'        => array(
        'name'      => 'sb_show_archive',
        'language'  => 'sb_show_archive',
        'property'  => 'checkbox',
    ),
    'pk_shoutbox_max_text_length'  => array(
      'name'      => 'sb_max_text_length',
      'language'  => 'sb_max_text_length',
      'property'  => 'text',
      'size'      => '3',
    ),
    'pk_shoutbox_input_box_location'  => array(
        'name'      => 'sb_input_box_location',
        'language'  => 'sb_input_box_location',
        'property'  => 'dropdown',
        'options'   => array(
              'top'    => 'sb_location_top',
              'bottom' => 'sb_location_bottom'
        ),
    ),
    'pk_shoutbox_autoreload'          => array(
        'name'      => 'sb_autoreload',
        'language'  => 'sb_autoreload',
        'property'  => 'text',
        'size'      => '3',
        'help'      => 'sb_autoreload_help',
    ),
  );
  /**
   * Installation
   */
  protected $install = array(
    'autoenable'      => '1',
    'defaultposition' => 'left2',
    'defaultnumber'   => '1',
  );

  /**
    * output
    * Get the portal output
    *
    * @returns string
    */
  public function output()
  {
    // initialize output
    $output = '';

    // check if shoutbox is installed
    if ($this->pm->check('shoutbox', PLUGIN_INSTALLED))
    {
      if (!class_exists('ShoutboxClass'))
        include_once($this->root_path.'plugins/shoutbox/includes/shoutbox.class.php');

      // create shoutbox
      $shoutbox = registry::register('ShoutboxClass');

      // do requirements check
      $requirementscheck = $shoutbox->checkRequirements();
      if ($requirementscheck !== true)
      {
        $output = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="colorswitch">
                     <tr>
                       <td><div class="center">'.$requirementscheck.'</div></td>
                     </tr>
                   </table>';
      }
	  //do permission check
      elseif (!$this->user->check_auth('u_shoutbox_view', false)){
		 $output = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="colorswitch">
                     <tr>
                       <td><div class="center">'.$this->user->lang('sb_no_view_permission').'</div></td>
                     </tr>
                   </table>';
	  }	
	  else 
      {
        // default position is none
        $position = '';

        // get the Shoutbox Portal ID
        $portal_ids = $this->pdh->get('portal', 'id_list', array(array('plugin' => 'shoutbox')));
        if (is_array($portal_ids) && count($portal_ids) > 0)
        {
          // get the position of the shoutbox portal module
          $position = $this->pdh->get('portal', 'position', array(array_pop($portal_ids)));
        }

        // output depending on position
        $orientation = '';
        switch ($position)
        {
        case 'left1':
        case 'left2':
        case 'right':
          $orientation = 'vertical';
          break;
        case 'middle':
        case 'bottom':
          $orientation = 'horizontal';
          break;
        default:
          $orientation = 'vertical';
          break;
        }

        // return the output for module
        $output = $shoutbox->showShoutbox($orientation);
      }
    }
    else
    {
      $output = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="colorswitch">
                   <tr>
                     <td><div class="center">'.$this->user->lang('sb_plugin_not_installed').'</div></td>
                   </tr>
                 </table>';
    }

    return $output;
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_shoutbox_portal', shoutbox_portal::__shortcuts());
?>
