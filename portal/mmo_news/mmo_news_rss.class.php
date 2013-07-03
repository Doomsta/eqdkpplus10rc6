<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-11-14 20:57:12 +0100 (Mi, 14. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12455 $
 * 
 * $Id: mmo_news_rss.class.php 12455 2012-11-14 19:57:12Z godmod $
 */


if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class mmo_news_rss extends gen_class {
	public static $shortcuts = array('core', 'user', 'time', 'jquery', 'html', 'tpl', 'config', 'puf'=>'urlfetcher', 'pdc');

	//Config
	var $cachetime			= 10800;	// refresh time in seconds default 3 hours = 10800 seconds
	var $tooltipcrop		= 60;		// after that number of symbols the text in the tooltip wraps
	var $titlecrop			= 30;		// after that number of symbols the text in the title wraps
	var $checkURL_first		= true ;
	var $middle				= false;
	var $moduleid			= 0;

	//return vars
	var $title 				= null;
	var $link 				= null;
	var $description		= null;
	var $lastcreate 		= null;
	var $feed				= null;
	var $news				= null;
	var $updated			= null;
	var $header				= '';
	var $output_left		= '';
	var $rssurl				= false;


	/**
	 * Constructor
	 *
	 * @return rss
	 */
	public function __construct($wherevalue, $moduleid){
		if ($wherevalue == 'middle'){
			$this->middle = true;
		}
		$this->moduleid = $moduleid;
		
		$this->output_left = 'No feed for your game or language available.';
		
		switch($this->config->get('game_language')){
			case 'german': {
						switch (strtolower($this->config->get('default_game'))){
							case 'wow': 				$rss_number = 1 ; break;
							case 'daoc': 				$rss_number = 7 ; break;
							case 'everquest': 			$rss_number = 10 ; break;
							case 'everquest2': 			$rss_number = 10 ; break;
							case 'lotro': 				$rss_number = 4 ; break;
							case 'tr': 					$rss_number = 19 ; break;
							case 'vanguard-soh': 		$rss_number = 5 ; break;
							case 'guildwars2': 			$rss_number = 52 ; break;
							case 'aoc': 				$rss_number = 13 ; break;
							case 'warhammer': 			$rss_number = 14 ; break;
							case 'aion': 				$rss_number = 22 ; break;
							case 'runesofmagic': 		$rss_number = 37 ; break;
							case 'swtor': 				$rss_number = 43 ; break;
							case 'diablo3': 			$rss_number = 39 ; break;
							case 'rift': 				$rss_number = 58 ; break;
							case 'tera': 				$rss_number = 55 ; break;
							
							default: $rss_number = 1 ;
								break;
						}
						
						$this->rssurl = 'http://rss.allvatar.com/news-'.$rss_number.'.xml';
			
			}
			break;
			
			case 'english': {
						switch (strtolower($this->config->get('default_game'))){
							case 'wow': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=15' ; break;
							case 'daoc': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=11' ; break;
							case 'everquest': 			$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=9' ; break;
							case 'everquest2': 			$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=2' ; break;
							case 'lotro': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=45' ; break;
							case 'vanguard-soh': 		$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=147' ; break;
							case 'guildwars2': 			$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=473' ; break;
							case 'aoc': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=191' ; break;
							case 'warhammer': 			$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=239' ; break;
							case 'aion': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=253' ; break;
							case 'runesofmagic': 		$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=351' ; break;
							case 'swtor': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=367' ; break;
							case 'diablo3': 			$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=644' ; break;
							case 'rift': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=431' ; break;
							case 'tera': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=477' ; break;
							case 'tsw' :				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=404' ; break;
						}
			}
			break;
			
			case 'spanish': {
						switch (strtolower($this->config->get('default_game'))){
							case 'wow': 				$this->rssurl = 'http://feeds.feedburner.com/guiaswowraideando' ; break;
						}
			}
			break;
		}
		
		if ($this->rssurl){
			$this->checkURL_first = $this->config->get('pm_mmo_news_checkURL') ;
			$this->parseXML($this->GetRSS($this->rssurl));
			if ($this->news){
				$this->createTPLvar($this->news);
			}
		}
	}

	/**
	 * GetRSS get the RSS Feed from an given URL
	 * Check if an refresh is needed
	 *
	 * @param String $url must be an valid RSS Feed
	 * @return XMLString
	 */
	private function GetRSS($url){
		$rss_string = $this->pdc->get('portal.mmo_news.rss');
		if ($rss_string == null) {
			//nothing cached or expired
			$this->tpl->add_js('$.get("'.$this->root_path.'portal/mmo_news/update.php");');
			//Is there some expired data?
			$expiredData = $this->pdc->get('portal.mmo_news.rss', false, false, true);
			$rss_string = ($expiredData != null) ? $expiredData : "";
		}

		return $this->decodeRSS($rss_string);
	}

	public function updateRSS(){
		$this->puf->checkURL_first = $this->checkURL_first;
		$rss_string = $this->puf->fetch($this->rssurl);
		$rss_string = is_utf8($rss_string) ? $rss_string : utf8_encode($rss_string);
		if ($rss_string && strlen($rss_string)>1){
			$this->pdc->put('portal.mmo_news.rss', @base64_encode($rss_string), $this->cachetime);
		} else {
			$this->pdc->put('portal.mmo_news.rss', "", $this->cachetime);
		}
	}

	private function decodeRSS($rss){
		if (!strlen($rss)) return '';
		$rss_string = @base64_decode($rss);
		return $rss_string;
	}

	/**
	 * parseXML
	 * parse the XML Data into an Array
	 *
	 * @param RSS-XML $rss
	 */
	private function parseXML($rss){
		if (!$rss OR $rss == 'ERROR') return false;
		$rss =  simplexml_load_string($rss);
		if(!is_object($rss)) return false;
		$this->title = $rss->channel->title;
		$this->link  = $rss->channel->link;
		$this->description = $rss->channel->description;
		$this->lastcreate =  $rss->channel->lastBuildDate;
		$this->feed	= $rss->channel->generator;
		$this->news = array();

		$count = ($this->config->get('pm_mmo_news_count')) ? intval($this->config->get('pm_mmo_news_count')) : 10;
		
		$i = 0;
		foreach($rss->channel->item as $item){
			if ($i >= $count){return;}
			$this->news[$i]['title']		= sanitize($item->title);
			$this->news[$i]['link']			= sanitize($item->link);
			$this->news[$i]['description']	= sanitize($item->description);
			$this->news[$i]['author']		= sanitize($item->author);
			$this->news[$i]['pubdate']		= sanitize($item->pubDate);
			$i++;
		}
	} # end function

	/**
	 * createTPLvar
	 * Createas the {NEWS_TICKER_H} and {NEWS_TICKER_V} Vars
	 * wich could be displayed in the templates
	 *
	 * @param Array $news
	 * @return NewstickerArray
	 */
	private function createTPLvar($news){
		$updated_time = $this->time->user_date($this->updated, true, true);
		$this->header = ucfirst($this->config->get('default_game')).'-News '.$updated_time ;

		if (is_array($news)){
			$newsticker_v_body = '';
			foreach ($news as $key => $value){
				// Generate an array fo an accordion
				// array style: title => content
				$newstick_array[(string)$value['title']] = $this->createBody(
					$value['description'],
					$value['link'],
					$value['author'],
					$value['pubdate']
				);

				$newsticker_v_body .= $this->createLink(
					$value['title'],
					$value['link'],
					$value['description'],
					$value['author'],
					$value['pubdate'],
					false
				) . " | ";
			}#  end foreach

			$table_title = " ";

			//ticker
			$newsticker_H  = '<div style="margin-bottom:10px; white-space: normal !important;" class="portalbox_head"> <marquee scrolldelay="110" onMouseover="javascript: this.scrollAmount=\'0\' " onMouseout="javascript: this.scrollAmount=\'8\'" >'.$newsticker_v_body;
			$newsticker_H .= '</marquee> </div>';

			//Menunews
			$newsticker_V = '<div style="white-space:normal;">'.$this->jquery->accordion('rrs_news',$newstick_array).'</div>';

			//Set Template Variables
			if ($this->middle){
				$this->tpl->assign_vars(array('NEWS_TICKER_H'			=> $newsticker_H));
				$this->tpl->add_js("$('#portalbox".$this->moduleid."').hide();", 'eop');
			} else {
				$this->output_left = $newsticker_V;
			}
		}
		return $newsticker_V ;
	} # end function

	/**
	 * createLink
	 * Creates an link with the description in a tooltip.
	 *
	 * @param String $title
	 * @param String $link
	 * @param  String $disc
	 * @return String
	 */
	private function createLink($title,$link,$disc,$author="",$date="",$crop_title=false){
		$tt = str_replace('"', "'", $disc);
		$tt = str_replace(array("\n", "\r"), '', $tt);

		$header = "<b>".$title."</b><br />";
		$content = $this->wrapText($tt,$this->tooltipcrop) ;
		$footer = "<br />".date('d.m.Y, H:i', strtotime($date))." by <b>".$author."</b>";

		if ($crop_title){
			$title =	$this->cropText($title,$this->titlecrop) ;
		}
		$_link = "<a href='".$link."' target='_blank'>".$title."</a>" ;
		$ret = " ".$this->html->ToolTip($header.$content.$footer,$_link);
		return $ret ;
	}

	/**
	 * createBody
	 *
	 * @param  String $disc
	 * @param  String $author
	 * @param  String $date
	 * @return String
	 */
	private function createBody($disc,$link,$author="",$date=""){
		$content = '<a href="'.$link.'" target="_blank">'.$this->cropText($disc,280).'</a>';
		$footer = $this->time->user_date(strtotime($date), true)." by <b>".$author."</b>";
		return $content.'<br />'.$footer;
	}

	/**
	 * cropText
	 * crop the text after a given lenght
	 *
	 * @param String $text
	 * @param Integer $len
	 * @return String
	 */
	private function cropText($text,$len){
		$ret = "";
		$ret = substr($text,0,$len);
		if (strlen($text) > $len){
			$ret .= '..';
		}
		return $ret ;
	}

	/**
	 * wrapText
	 * wraps the text after a given lenght
	 * but only after an word!
	 *
	 * @param String $text
	 * @param Integer $len
	 * @return String
	 */
	private function wrapText($text,$len=60){
		$croplen = $len ;
		$ret = "";
		for($i=0;;$i++){
			if ($i == strlen($text)) {
				break;
			}

			$ret .= $text[$i];
			if ($i >= $len){
				if ($text[$i] == " "){
					$ret .= str_replace(' ', '<br />', $text[$i] );
					$len = $len+$croplen;
				}
			}
		}
		return $ret ;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_mmo_news_rss', mmo_news_rss::$shortcuts);
?>