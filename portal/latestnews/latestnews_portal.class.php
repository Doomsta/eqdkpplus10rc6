<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-11-11 19:07:23 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12435 $
 * 
 * $Id: latestnews_portal.class.php 12435 2012-11-11 18:07:23Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class latestnews_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'pdh', 'core', 'html', 'time', 'config', 'comments'=>'comments');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'latestnews';
	protected $data		= array(
		'name'			=> 'Latest News',
		'version'		=> '0.1.0',
		'author'		=> 'GodMod',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Displays the latest news',
	);
	protected $positions = array('middle', 'left1', 'left2', 'right', 'bottom');
	protected $settings	= array(
		'pk_latestnews_amount'	=> array(
			'name'		=> 'pk_latestnews_amount',
			'language'	=> 'pk_latestnews_amount',
			'property'	=> 'text',
			'size'		=> '6',
			'help'		=> '',
			'value'		=> '5',
		),
		'pk_latestnews_showtooltip'	=> array(
			'name'		=> 'pk_latestnews_showtooltip',
			'language'	=> 'pk_latestnews_showtooltip',
			'property'	=> 'checkbox',
			'selected'	=> '1',
			'help'		=> '',
		),
	);
	protected $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '1',
	);

	public function __construct($position=''){
		parent::__construct($position);
	}

	public function output() {
			$myOut = '';
			$number = ($this->config->get('pk_latestnews_amount')) ? (int)$this->config->get('pk_latestnews_amount') : 5;
			$showToolTip = ($this->config->get('pk_latestnews_showtooltip') !== false) ? (int)$this->config->get('pk_latestnews_showtooltip') : true;
			$allnews = $this->pdh->aget('news', 'news', 0, array($this->pdh->sort($this->pdh->get('news', 'id_list'), 'news', 'date', 'desc')));
			$i = 0;
			foreach($allnews as $nid => $new) {
				if((!$new['news_start'] OR ($new['news_start'] AND $new['news_start'] < $this->time->time)) AND (!$new['news_stop'] OR ($new['news_stop'] AND $new['news_stop'] > $this->time->time)) && $this->pdh->get('news', 'has_permission', array($nid)) && $i<$number) {
					$news_array[$nid] = $new;
					$i++;
				}
			}
			if (is_array($news_array)){
				$myOut = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="colorswitch">';
				foreach ($news_array as $news_id => $news){
					$wraptext = '<a href="'.$this->root_path.'viewnews.php'.$this->SID.'&amp;id='.$news_id.'">'.$news['news_headline'].'</a>';
					$tooltip = sprintf($this->user->lang('news_submitter'), sanitize($news['username']), $this->time->user_date($news['news_date'], false, true));
					
					$nocomments = (isset($news['nocomments'])) ? $news['nocomments'] : 0;
					if ($nocomments != 1){
						// get the count of comments per news:
						$this->comments->SetVars(array('attach_id'=>$news['news_id'], 'page'=>'news'));
						$comcount = $this->comments->Count();
						$comments_counter = ($comcount == 1 ) ? $comcount.' '.$this->user->lang('news_comment') : $comcount.' '.$this->user->lang('news_comments') ;
						$comments_counter = ($comcount == 1 ) ? $comcount.' '.$this->user->lang('news_comment') : $comcount.' '.$this->user->lang('news_comments') ;
						$tooltip .= '<br />'.$comments_counter;
					}
					$category = ($this->config->get('enable_newscategories') == 1) ? $tooltip .= '<br />'.$this->user->lang('category').': '.$news['news_category'] : '';
					$content = ($showToolTip) ? $this->html->ToolTip($tooltip, $wraptext) : $wraptext;
					$myOut .= '<tr><td>'.$this->time->user_date($news['news_date']).'</td><td>'.$content.'</td></tr>';
				}
				$myOut .= '</table>';
			}
		return $myOut;
	}

}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_latestnews_portal', latestnews_portal::__shortcuts());
?>