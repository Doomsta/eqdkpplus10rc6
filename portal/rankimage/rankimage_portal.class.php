<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-05-01 13:28:27 +0200 (Di, 01. Mai 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy_leon $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11769 $
 * 
 * $Id: rankimage_portal.class.php 11769 2012-05-01 11:28:27Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class rankimage_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'rankimage';
	protected $data		= array(
		'name'			=> 'Rank Image',
		'version'		=> '1.1.0',
		'author'		=> 'Corgan',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Shows a rank image in the portal',
	);
	protected $positions = array('left1', 'left2', 'right');
	protected $settings	= array(
		'pk_ts_ranking_link'	=> array(
			'name'		=> 'pk_ts_ranking_link',
			'language'	=> 'pk_ts_ranking_link',
			'property'	=> 'textarea',
			'cols'		=> '30',
			'rows'		=> '3'
		),
		'uc_servername'	=> array(
			'name'		=> 'pk_ts_ranking_url',
			'language'	=> 'pk_ts_ranking_url',
			'property'	=> 'textarea',
			'cols'		=> '30',
			'rows'		=> '3',
			'options'	=> false,
		),
		'pk_ts_bosskillers'	=> array(
			'name'		=> 'pk_ts_bosskillers',
			'language'	=> 'pk_ts_bosskillers',
			'property'	=> 'checkbox',
			'options'	=> false,
		),
	);
	protected $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '7',
	);

	public function output() {
		if ($this->config->get('pk_ts_ranking_url') && $this->config->get('pk_ts_ranking_link')){
			$out .= '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="noborder">';
			$out .= '<tr ><td align=center>';

			if($this->config->get('pk_ts_bosskillers')==true){
				$pyyri_tmp	= $this->config->get('pk_ts_ranking_url');
				$pyyri_tmp	= str_replace('&lt;', '<', $pyyri_tmp);
				$pyyri_tmp	= str_replace('&gt;', '>', $pyyri_tmp);
				$pyyri_tmp	= str_replace('&quot;', '"', $pyyri_tmp);
				$out .= $pyyri_tmp;
			} else {
				if(strlen($this->config->get('pk_ts_ranking_link') > 0) || $this->config->get('pk_ts_ranking_link')){
					$out .= '<a href="'.$this->config->get('pk_ts_ranking_link').'" target=_blank> <img src="'.$this->config->get('pk_ts_ranking_url').'"> </a>';
				}else{
					$out .= '<img src="'.$this->config->get('pk_ts_ranking_url').'">';
				}
			}
			$out .= '</td></tr>';
			$out .= '</table>';
			return $out;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_rankimage_portal', rankimage_portal::__shortcuts());
?>