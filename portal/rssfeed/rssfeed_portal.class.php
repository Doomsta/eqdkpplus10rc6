<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-11-04 18:53:42 +0100 (So, 04. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12393 $
 *
 * $Id: rssfeed_portal.class.php 12393 2012-11-04 17:53:42Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class rssfeed_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'core', 'pfh', 'pm', 'jquery', 'tpl', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'rssfeed';
	protected $data		= array(
		'name'			=> 'RSS Feed',
		'version'		=> '2.0.0',
		'author'		=> 'WalleniuM',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Shows an RSS Feed in portal',
	);
	protected $positions = array('left1', 'left2', 'right');
	protected $settings	= array(
		'pk_rssfeed_url'	=> array(
			'name'		=> 'pk_rssfeed_url',
			'language'	=> 'pk_rssfeed_url',
			'property'	=> 'text',
			'size'		=> '40',
			'help'		=> '',
		),
		'pk_rssfeed_limit'	=> array(
			'name'		=> 'pk_rssfeed_limit',
			'language'	=> 'pk_rssfeed_limit',
			'property'	=> 'text',
			'size'		=> '5',
			'help'		=> '',
		),
		'pk_rssfeed_length'	=> array(
			'name'		=> 'pk_rssfeed_length',
			'language'	=> 'pk_rssfeed_length',
			'property'	=> 'text',
			'size'		=> '3',
			'help'		=> 'pk_rssfeed_length_h',
		)
	);
	protected $install	= array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'left2',
		'defaultnumber'		=> '9',
	);

	public function output() {
		if($this->config->get('pk_rssfeed_url')){
			$pk_rssfeed_limit = ($this->config->get('pk_rssfeed_limit')) ? $this->config->get('pk_rssfeed_limit') : 5;
			$pk_rssfeed_length = ($this->config->get('pk_rssfeed_length')) ? $this->config->get('pk_rssfeed_length') : 80;
			$this->tpl->add_css("
				#rssfeed_module{
					margin:0;
					padding:5px;
					height:200px;
					overflow: auto;
				}
				#rssfeed_module a{
					color:#FF9900;
					font-size:11px;
				}
				#rssfeed_module .rss_readmore{
					color:#f5f5f5;
					font-size:10px;
					margin-bottom: 5px;
				}
				#rssfeed_module .date{
					margin:0;
					color:#999999;
					font-size:9px;
				}
				#rssfeed_module .description{
					margin:0;
					padding:0;
				}
				#rssfeed_module .description p{
					font-size:10px;
				}
				.loading{
					margin:20% 0% 0% 40%;
					float:left;
				}");
			$output = '<div id="rssfeed_module"></div>';

			// JS Part
			$this->jquery->rssFeeder('rssfeed_module', $this->root_path."portal/rssfeed/load.php".$this->SID."&loadrss=true", $pk_rssfeed_limit, $pk_rssfeed_length);
		}else{
			$output  = $this->user->lang('pk_rssfeed_nourl');
		}
		return $output;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_rssfeed_portal', rssfeed_portal::__shortcuts());
?>