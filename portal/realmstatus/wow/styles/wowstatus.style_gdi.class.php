<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date: 2012-11-14 20:57:12 +0100 (Mi, 14. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2010-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 12455 $
 *
 * $Id: wowstatus.style_gdi.class.php 12455 2012-11-14 19:57:12Z godmod $
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
  | wowstatus_style_gdi
  +--------------------------------------------------------------------------*/
if (!class_exists("wowstatus_style_gdi"))
{
  class wowstatus_style_gdi extends wowstatus_style_base
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('user', 'tpl', 'pdc', 'pfh');
      return array_merge(parent::$shortcuts, $shortcuts);
    }

    /* Base image path */
    private $image_path;

    /* cache time in seconds default 24 hours = 1800 seconds */
    private $cachetime = 86400;

    /**
     * Constructor
     */
    public function __construct()
    {
      // call base constructor
      parent::__construct();

      // set image path
      $this->image_path = $this->root_path.'portal/realmstatus/wow/images/gdi/';
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
          // create the image for the realm
          $this->processImage($realmname, $realmdata);

          // set output image
          $image_file = $this->getImageFile($realmname, true);
          $output .= '<div class="center rs_wow">
                        <img src="'.$image_file.'" alt="WoW-'.$this->user->lang('realmstatus').': '.$realmname.'" title="'.$realmname.'" />
                      </div>';
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
      $style = '.rs_wow {
                  margin-top:    2px;
                  margin-bottom: 2px;
                }';

      // add css
      $this->tpl->add_css($style);
    }

    /**
     * processImage
     * Create the image if it does not exist or has changed
     *
     * @param  string  $realmname  Name of the Realm
     * @param  array   $realmdata  Array with Realm data
     */
    private function processImage($realmname, $realmdata)
    {
      // set cache to false, this will update if something has changed
      $do_cache = false;

      // try to load data from cache
      $realm_status = $this->pdc->get('portal.module.realmstatus.wow.gdi', false, true);

      // if either no cache, realmname not in list or realm status has changed, update the image
      if (!$realm_status ||
          !isset($realm_status[$realmname]) ||
          $realm_status[$realmname] != $realmdata['status'] ||
          !file_exists($this->getImageFile($realmname)))
      {
        // update the image
        $this->createImage($realmname, $realmdata);

        // update the cache
        $realm_status[$realmname] = $realmdata['status'];
        $do_cache = true;
      }

      // store data within cache
      if ($do_cache && is_array($realm_status))
        $this->pdc->put('portal.module.realmstatus.wow.gdi', $realm_status, $this->cachetime, false, true);
    }

    /**
     * createImage
     * Create the image
     *
     * @param  string  $realmname  Name of the Realm
     * @param  array   $realmdata  Array with Realm data
     */
    private function createImage($realmname, $realmdata)
    {
      // get status
      switch ($realmdata['status'])
      {
        case 0:  $status = 'down';    break;
        case 1:  $status = 'up';      break;
        default: $status = 'unknown'; break;
      }

      // get population
      // TODO: check if offline is available
      if ($status == 'down')
        $population = 'offline';
      else
        $population = strtolower($realmdata['population']);

      // get type
      switch ($realmdata['type'])
      {
        case 'pve':   $type = 'PvE';    break;
        case 'pvp':   $type = 'PvP';    break;
        case 'rp':    $type = 'RP';     break;
        case 'rppvp': $type = 'RP PvP'; break;
        default:      $type = '';       break;
      }


      // get fonts
      $server_font = $this->image_path.'silkscreen.ttf';
      $type_font   = $this->image_path.'silkscreenb.ttf';

      // load back image
      $back_image = imagecreatefrompng($this->image_path.$status.'.png');
      $back_width  = imagesx($back_image);
      $back_height = imagesy($back_image);

      // load bottom image
      $bottom_image = imagecreatefrompng($this->image_path.$status.'2.png');
      $bottom_width  = imagesx($bottom_image);
      $bottom_height = imagesy($bottom_image);

      // load population image
      $population_image = imagecreatefrompng($this->image_path.$population.'.png');
      $population_width  = imagesx($population_image);
      $population_height = imagesy($population_image);

      // create new image
      $image = imagecreate($back_width, $back_height + $bottom_height);

      // create the background color
      $background_color = imagecolorallocate($image, 0, 255, 255);
      // set transparent color
      imagecolortransparent($image, $background_color);

      // copy back and bottom image to the target
      imagecopy($image, $back_image,   0,            0, 0, 0, $back_width,   $back_height);
      imagecopy($image, $bottom_image, 0, $back_height, 0, 0, $bottom_width, $bottom_height);

      // create the colors
      $text_color   = imagecolorallocate($image,  51,  51,  51);
      $shadow_color = imagecolorallocate($image, 255, 204,   0);

      // copy the population image to the target (center)
      $dest_x = round(($back_width - $population_width) / 2);
      imagecopy($image, $population_image, $dest_x, 62, 0, 0, $population_width, $population_height);

      // close back / bottom / population image
      imagedestroy($back_image);
      imagedestroy($bottom_image);
      imagedestroy($population_image);

      // Ouput centered server name
      $max_width = 62;
      $box = imagettfbbox(6, 0, $server_font, $realmname);
      $w = abs($box[0]) + abs($box[2]);
      if ($w > $max_width)
      {
        $i = $w;
        $t = strlen($realmname);
        while ($i > $max_width)
        {
          $t--;
          $box = imagettfbbox(6, 0, $server_font, substr($realmname, 0, $t));
          $i = abs($box[0]) + abs($box[2]);
        }

        $t = strrpos(substr($realmname, 0, $t), ' ');

        $output[0] = substr($realmname, 0, $t);
        $output[1] = ltrim(substr($realmname, $t));
        $vadj = -6;
      }
      else
      {
        $output[0] = $realmname;
        $vadj = 0;
      }

      $i = 0;
      foreach($output as $value)
      {
        $box = imagettfbbox(6, 0, $server_font, $value);
        $w = abs($box[0]) + abs($box[2]);

        imagettftext($image, 6, 0, round(($back_width-$w)/2)+1, 58+($i*8)+$vadj, $shadow_color, $server_font, $value);
        imagettftext($image, 6, 0, round(($back_width-$w)/2),   57+($i*8)+$vadj,  -$text_color, $server_font, $value);
        $i++;
      }

      // Ouput centered $type
      if ($type != '')
      {
        $box = imagettfbbox(6, 0, $type_font, $type);
        $w = abs($box[0]) + abs($box[2]);
        imagettftext($image, 6, 0, round(($back_width-$w)/2)+1, 85, $shadow_color, $type_font, $type);
        imagettftext($image, 6, 0, round(($back_width-$w)/2),   84, -$text_color,  $type_font, $type);
      }

      // save image
      $image_file = $this->getImageFile($realmname);
	  $tmp_image_file = $this->getTmpImageFile($realmname);
	  
      imagepng($image, $tmp_image_file);
	  
	  $this->pfh->FileMove($tmp_image_file, $image_file);
      // destroy
      imagedestroy($image);
    }

    /**
     * getImageFile
     * Get the file path for the specified realm image
     *
     * @param  string  $realmname  Name of the Realm
     *
     * @return  string
     */
    private function getImageFile($realmname, $absolute=false)
    {
      $filename = strtolower($realmname);
      $filename = str_replace(array('\'', ' '), array('_', '_'), $filename);
      return ($absolute) ? $this->pfh->FileLink($this->pfh->FolderPath('wow', 'realmstatus').$filename.'.png', false, 'absolute') : $this->pfh->FolderPath('wow', 'realmstatus').$filename.'.png';
    }
	
	private function getTmpImageFile($realmname){
	  $filename = strtolower($realmname);
      $filename = str_replace(array('\'', ' '), array('_', '_'), $filename);
	  $tmp_file = $this->pfh->FolderPath('tmp', '').'realmstatus_'.$filename.'.png';
	  return $tmp_file;
	}

  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_wowstatus_style_gdi', wowstatus_style_gdi::__shortcuts());
?>
