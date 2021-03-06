<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2013-07-03 09:41:23 +0100 $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * Castle mod by Doomsta2k7
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12479 $
 * 
 * $Id: bnet_armory.class.php 12479 2012-11-19 08:41:23Z wallenium $
 *
 * Based on the new battlenet API, see documentation: http://blizzard.github.com/api-wow-docs/
 */

/*********** TODO ************ 
- testing of the header sending & API KEY
******************************/

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class bnet_armory {

	private $version		= '5.0';
	private $build			= '$Rev: 12479 $';
	private $chariconUpdates = 0;
	private $chardataUpdates = 0;
	const apiurl			= 'http://{region}.battle.net/api/';
	const staticrenderurl	= 'http://{region}.battle.net/static-render/';
	const tabardrenderurl	= 'http://{region}.battle.net/wow/static/images/guild/tabards/';

	private $_config		= array(
		'serverloc'				=> 'us',
		'locale'				=> 'en',
		'caching'				=> true,
		'caching_time'			=> 24,
		'apiUrl'				=> '',
		'apiRenderUrl'			=> '',
		'apiTabardRenderUrl'	=> '',
		'apiKeyPrivate'			=> '',
		'apiKeyPublic'			=> '',
		'maxChariconUpdates'	=> 10,
		'maxChardataUpdates'	=> 10,
	);

	protected $convert		= array(
		'classes' => array(
			1		=> '10',	// warrior
			2		=> '5',		// paladin
			3		=> '3',		// hunter
			4		=> '7',		// rogue
			5		=> '6',		// priest
			6		=> '1',		// DK
			7		=> '8',		// shaman
			8		=> '4',		// mage
			9		=> '9',		// warlock
			11		=> '2',		// druid
			10		=> '11',	//monk
		),
		'races' => array(
			'1'		=> 2,		// human
			'2'		=> 7,		// orc
			'3'		=> 3,		// dwarf
			'4'		=> 4,		// night elf
			'5'		=> 6,		// undead
			'6'		=> 8,		// tauren
			'7'		=> 1,		// gnome
			'8'		=> 5,		// troll
			'9'		=> 12,		// Goblin
			'10'	=> 10,		// blood elf
			'11'	=> 9,		// draenei
			'22'	=> 11,		// Worgen
			'24'	=> 13,		// Pandaren neutral
			'25'	=> 13,		// Pandaren alliance
			'26'	=> 13,		// Pandaren horde
		),
		'gender' => array(
			'0'		=> 'Male',
			'1'		=> 'Female',
		),
		'talent'	=> array(
			0	=> array(
				'spell_deathknight_bloodpresence',			// DK
				'spell_nature_starfall',					// Druid
				'ability_hunter_bestialdiscipline',			// Hunter
				'spell_holy_magicalsentry',					// Mage
				'spell_holy_holybolt',						// Paladin
				'spell_holy_powerwordshield',				// Priest
				'ability_rogue_eviscerate',					// Rogue
				'spell_nature_lightning',					// Shaman
				'spell_shadow_deathcoil',					// Warlock
				'ability_warrior_savageblow',				// Warrior
				'spell_monk_brewmaster_spec',				// Monk
			),
			1	=> array(
				'spell_deathknight_frostpresence',			// DK
				'ability_druid_catform',					// Druid
				'ability_hunter_focusedaim',				// Hunter
				'spell_fire_firebolt02',					// Mage
				'ability_paladin_shieldofthetemplar',		// Paladin
				'spell_holy_guardianspirit',				// Priest
				'ability_backstab',							// Rogue
				'spell_shaman_improvedstormstrike',			// Shaman
				'spell_shadow_metamorphosis',				// Warlock
				'ability_warrior_innerrage',				// Warrior
				'spell_monk_mistweaver_spec',				// Monk
			),
			2	=> array(
				'spell_deathknight_unholypresence',			// DK
				'ability_racial_bearform',					// Druid
				'ability_hunter_camouflage',				// Hunter
				'spell_frost_frostbolt02',					// Mage
				'spell_holy_auraoflight',					// Paladin
				'spell_shadow_shadowwordpain',				// Priest
				'ability_stealth',							// Rogue
				'spell_nature_magicimmunity',				// Shaman
				'spell_shadow_rainoffire',					// Warlock
				'ability_warrior_defensivestance',			// Warrior
				'spell_monk_windwalker_spec',				// Monk
			),
			3	=> array('spell_nature_healingtouch')		// Druid
		),
				'gearSlotNr' => array(
			'head' => 0, 
			'neck' => 1,
			'shoulder' => 2,
			'back' => 14,
			'chest' => 4,
			'shirt' => 3,
			'tabard' => 18,
			'wrist' => 8,
			
			'hands' => 9,
			'waist' => 5, //belt
			'legs' => 6,
			'feet' => 7,
			'finger1' => 10,
			'finger2' => 11,
			'trinket1' => 12,
			'trinket2' => 13,
			
			'mainHand' => 15,
			'offHand' => 16,
			'relik' => 17
		),
		'professionIcons' => array(
			'inscription' => 'inv_inscription_tradeskill01',
			'3' => 'inv_misc_gem_01', //TODO
			'skinning' => 'inv_misc_pelt_wolf_01',
			'mining' => 'inv_pick_02',
			'alchemy' => 'trade_alchemy',
			'blacksmithing' => 'trade_blacksmithing',
			'engineering' => 'trade_engineering',
			'enchanting' => 'trade_engraving',
			'herbalism' => 'trade_herbalism',
			'leatherworking' => 'trade_leatherworking',
			'tailoring' => 'trade_tailoring'
		),
	);

	private $serverlocs		= array(
		'eu'	=> 'EU',
		'us'	=> 'US',
		'kr'	=> 'KR',
		'tw'	=> 'TW',
	);
	private $converts		= array();

	/**
	* Initialize the Class
	* 
	* @param $serverloc		Location of Server
	* @param $locale		The Language of the data
	* @return bool
	*/
	public function __construct($serverloc='us', $locale='en_EN', $apikeys=false){
		$this->_config['serverloc']	= ($serverloc != '') ? $serverloc : 'en_EN';
		$this->_config['locale']	= $locale;
		$this->setApiUrl($this->_config['serverloc']);
		if(isset($apikeys['apiKeyPrivate']) && isset($apikeys['apiKeyPublic'])){
			$this->_config['apiKeyPrivate']	= $apikeys['apiKeyPrivate'];
			$this->_config['apiKeyPublic']	= $apikeys['apiKeyPublic'];
		}
	}
	
	public function __get($name) {
		if(class_exists('registry')) {
			if($name == 'pfh') return registry::register('file_handler');
			if($name == 'puf') return registry::register('urlfetcher');
		}
		return null;
	}

	/**
	* Set some settings
	* 
	* @param $setting	Which language to import
	* @return bool
	*/
	public function setSettings($setting){
		if(isset($setting['loc'])){
			$this->_config['serverloc']	= $setting['loc'];
			$this->setApiUrl($this->_config['serverloc']);
		}
		if(isset($setting['locale'])){
			$this->_config['locale']	= $setting['locale'];
		}
		if(isset($setting['caching_time'])){
			$this->_config['caching_time']	= $setting['caching_time'];
		}
		if(isset($setting['caching'])){
			$this->_config['caching']	= $setting['caching'];
		}
		if(isset($setting['apiKeyPrivate']) && isset($setting['apiKeyPublic'])){
			$this->_config['apiKeyPrivate']	= $setting['apiKeyPrivate'];
			$this->_config['apiKeyPublic']	= $setting['apiKeyPublic'];
		}
	}

	public function getServerLoc(){
		return $this->serverlocs;
	}

	public function getVersion(){
		return $this->version.((preg_match('/\d+/', $this->build, $match))? '#'.$match[0] : '');
	}

	/**
	* Generate Link to Armory
	* 
	* @param $user			Name of the User
	* @param $server		Name of the WoW Server
	* @param $mode			Which page to open? (char, talent, statistics, reputation, guild, achievements)
	* @param $guild			Name of the guild
	* @return string		output
	*/
	public function bnlink($user, $server, $mode='char', $guild='', $talents=array()){
		$linkprfx	= str_replace('/api', '/wow', $this->_config['apiUrl']);
		switch ($mode) {
			case 'char':
				return $linkprfx.sprintf('character/%s/%s/simple', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'talent':
				return $linkprfx.sprintf('character/%s/%s/simple#talents', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'statistics':
				return $linkprfx.sprintf('character/%s/%s/statistic', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'profession':
				return $linkprfx.sprintf('character/%s/%s/profession/', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'reputation':
				return $linkprfx.sprintf('character/%s/%s/reputation', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'pvp':
				return $linkprfx.sprintf('character/%s/%s/pvp', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'achievements':
				return $linkprfx.sprintf('character/%s/%s/achievement', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'character-feed':
				return $linkprfx.sprintf('character/%s/%s/feed', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
			case 'talent-calculator':
				return $linkprfx.sprintf('tool/talent-calculator#d%s!%s!%s', $talents['calcSpec'], $talents['calcTalent'], $talents['calcGlyph']);break;
			case 'guild':
				return $linkprfx.sprintf('guild/%s/%s/roster', $this->ConvertInput($server, true, true), $this->ConvertInput($guild));break;
			case 'guild-achievements':
				return $linkprfx.sprintf('guild/%s/%s/achievement', $this->ConvertInput($server, true, true), $this->ConvertInput($guild));break;
			case 'askmrrobot':
			return sprintf('http://www.askmrrobot.com/wow/gear/%s/%s/%s', $this->_config['serverloc'], $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
		}
	}

	/**
	* Return an array with all links for one char
	* 
	* @param $user			Name of the User
	* @param $server		Name of the WoW Server
	* @return string		output
	*/
	public function a_bnlinks($user, $server, $guild=false){
		return array(
			//'profil'			=> $this->bnlink($user, $server, 'char'),
			'profil'			=> 'http://armory.wow-castle.de/character-sheet.xml?r=WoW-Castle+PvE&cn='.$user,
			//'talents'			=> $this->bnlink($user, $server, 'talent'),
			'talents'			=> 'http://armory.wow-castle.de/character-talents.xml?r=WoW-Castle+PvE&cn='.$user,
			//'profession'			=> $this->bnlink($user, $server, 'profession'),
			'profession'			=> 'http://armory.wow-castle.de/character-sheet.xml?r=WoW-Castle+PvE&cn='.$user,
			//'reputation'			=> $this->bnlink($user, $server, 'reputation'),
			'reputation'			=> 'http://armory.wow-castle.de/character-reputation.xml?r=WoW-Castle+PvE&cn='.$user,
			//'pvp'				=> $this->bnlink($user, $server, 'pvp'),
			'pvp'				=> 'http://armory.wow-castle.de/character-arenateams.xml?r=WoW-Castle+PvE&cn='.$user,
			//'achievements'		=> $this->bnlink($user, $server, 'achievements'),
			'achievements'			=> 'http://armory.wow-castle.de/character-achievements.xml?r=WoW-Castle+PvE&cn='.$user,
			//'statistics'			=> $this->bnlink($user, $server, 'statistics'),
			'statistics'			=> 'http://armory.wow-castle.de/character-statistics.xml?r=WoW-Castle+PvE&cn='.$user,
			//'character-feed'		=> $this->bnlink($user, $server, 'character-feed'),
			'character-feed'		=> 'http://armory.wow-castle.de/character-feed.xml?r=WoW-Castle+PvE&cn=Doomsta'.$user,
			//'guild'			=> $this->bnlink($user, $server, 'guild', $guild),
			'guild'				=> 'http://armory.wow-castle.de/guild-info.xml?r=WoW-Castle+PvE&gn='.$guild,

			// external ones
			//'askmrrobot'			=> $this->bnlink($user, $server, 'askmrrobot'),
		);
	}

	/**
	* Fetch character information
	* 
	* @param $user		Character Name
	* @param $realm		Realm Name
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function character($user, $realm, $force=false){
		$user	= ucfirst(strtolower($this->ConvertInput($user)));
		$url = 'http://armory.wow-castle.de/character-sheet.xml?r=WoW-Castle+PvE&cn='.$user;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Language: de-de, de;"));
		$content = curl_exec ($ch);
		curl_close ($ch);
		$xml = new SimpleXMLElement($content);
		
		$chardata = array();
		$chardata['lastModified] '] = (string) $xml->characterInfo->character['lastModified']; 
		$chardata['name'] = (string)  $xml->characterInfo->character['name']; 
		$chardata['realm'] =  (string) $xml->characterInfo->character['realm'];
		$chardata['battleGroup'] =  (string) $xml->characterInfo->character['battleGroup'];
		$chardata['class'] =  (string) $xml->characterInfo->character['classId'];
		$chardata['race'] = (int) $xml->characterInfo->character['raceId'];
		$chardata['level'] = (int) $xml->characterInfo->character['level'];
		$chardata['achievementPoints'] = (int) $xml->characterInfo->character['points'];
        $chardata['title'] = (string) $xml->characterInfo->character['prefix'];
        //$chardata['thumbnail'] = NULL;
		//$chardata['calcClass']  = NULL;

		$chardata['guild'] = array();
		$chardata['guild']['name'] = (string) $xml->characterInfo->character['guildName'];
		$chardata['guild']['realm'] = (string) $xml->characterInfo->character['realm'];
		$chardata['guild']['battlegroup'] = (string) $xml->characterInfo->character['battleGroup'];
		//$chardata['guild']['level'] = null; //Cata
		$chardata['guild']['members'] = NULL;
		//$chardata['guild']['achievementPoints'] = null; //cata
		$chardata['guild']['emblem']= array();
		$chardata['guild']['emblem']['icon'] = NULL;
		$chardata['guild']['emblem']['iconColor'] = NULL;
		$chardata['guild']['emblem']['border'] = NULL;
		$chardata['guild']['emblem']['borderColor'] = NULL;
		$chardata['guild']['emblem']['backgroundColor'] = NULL;
		/*
			$chardata['feed'][$i] = array();
			$chardata['feed'][$i]['type'] = "BOSSKILL";
			$chardata['feed'][$i]['timestamp'] = 
			$chardata['feed'][$i]['achievement'] = array();
			$chardata['feed'][$i]['achievement']['id'] = null;
			$chardata['feed'][$i]['achievement']['title'] = $feed->entry->title;
			$chardata['feed'][$i]['achievement']['icon'] = "trade_engineering";
		*/
		//gear
		$gear = array();
		foreach($xml->characterInfo->characterTab->items->item as $item) {
			$gear[(string)$item['slot']] = array();
			$gear[(string)$item['slot']]['id'] = (string)$item['id'];
			$gear[(string)$item['slot']]['name'] = (string)$item['name'];
			$gear[(string)$item['slot']]['level'] = (string)$item['level'];
			
			$gear['iLevelSum'] += $item['level'];
		}		
		$chardata['items']['averageItemLevel'] = null;		
		$chardata['items']['averageItemLevelEquipped'] = round(($gear['iLevelSum']/17),0);
		//TODO 2H WEAP $chardata['items']['averageItemLevelEquipped'] = round(($gear['iLevelSum']/16),0);
		
		$chardata['items']['head']['id'] = $gear[$this->convert['gearSlotNr']['head']]['id'];
		$chardata['items']['neck']['id'] = $gear[$this->convert['gearSlotNr']['neck']]['id'];
		$chardata['items']['shoulder']['id'] = $gear[$this->convert['gearSlotNr']['shoulder']]['id'];		
		$chardata['items']['back']['id'] = $gear[$this->convert['gearSlotNr']['back']]['id'];
		$chardata['items']['chest']['id'] = $gear[$this->convert['gearSlotNr']['chest']]['id'];
		$chardata['items']['shirt']['id'] = $gear[$this->convert['gearSlotNr']['shirt']]['id'];
		$chardata['items']['tabard']['id'] = $gear[$this->convert['gearSlotNr']['tabard']]['id'];
		$chardata['items']['wrist']['id'] = $gear[$this->convert['gearSlotNr']['wrist']]['id'];
		$chardata['items']['hands']['id'] = $gear[$this->convert['gearSlotNr']['hands']]['id'];
		$chardata['items']['waist']['id'] = $gear[$this->convert['gearSlotNr']['waist']]['id'];
		$chardata['items']['legs']['id'] = $gear[$this->convert['gearSlotNr']['legs']]['id'];
		$chardata['items']['feet']['id'] = $gear[$this->convert['gearSlotNr']['feet']]['id'];
		$chardata['items']['finger1']['id'] = $gear[$this->convert['gearSlotNr']['finger1']]['id'];
		$chardata['items']['finger2']['id'] = $gear[$this->convert['gearSlotNr']['finger2']]['id'];
		$chardata['items']['trinket1']['id'] = $gear[$this->convert['gearSlotNr']['trinket1']]['id'];
		$chardata['items']['trinket2']['id'] = $gear[$this->convert['gearSlotNr']['trinket2']]['id'];
		$chardata['items']['mainHand']['id'] = $gear[$this->convert['gearSlotNr']['mainHand']]['id'];
		$chardata['items']['offHand']['id'] = $gear[$this->convert['gearSlotNr']['offHand']]['id'];
		$chardata['items']['relik']['id'] = $gear[$this->convert['gearSlotNr']['relik']]['id'];
		
		$chardata['stats'] = array();
		$chardata['stats']['health'] =  (string) $xml->characterInfo->characterTab->characterBars->health['effective'];  
		//powerType
		$var = (string) $xml->characterInfo->characterTab->characterBars->secondBar['type']; 
		switch ($var) {
			case m: //mana
				$chardata['stats']['powerType'] = 'mana';
				break;
			case r: //wut
				$chardata['stats']['powerType'] = 'rage';
				break;
			case e: //energie
				$chardata['stats']['powerType'] = 'energy';
				break;    
			case p: //runenmacht
				$chardata['stats']['powerType'] = 'runic-power';
				break;
		}
		
		$chardata['stats']['power'] =  (string)$xml->characterInfo->characterTab->characterBars->secondBar['effective'];
		//base stats 
		$chardata['stats']['str'] = (string) $xml->characterInfo->characterTab->baseStats->strength['effective'];  
		$chardata['stats']['agi'] = (string) $xml->characterInfo->characterTab->baseStats->agility['effective']; 
		$chardata['stats']['sta'] = (string) $xml->characterInfo->characterTab->baseStats->stamina['effective']; 
		$chardata['stats']['int'] = (string) $xml->characterInfo->characterTab->baseStats->intellect['effective']; 
		$chardata['stats']['spr'] = (string) $xml->characterInfo->characterTab->baseStats->spirit['effective'];  
		
		//melee
		$chardata['stats']['mainHandDmgMin'] = (string) $xml->characterInfo->characterTab->melee->mainHandDamage['min'];
		$chardata['stats']['mainHandDmgMax'] = (string) $xml->characterInfo->characterTab->melee->mainHandDamage['max'];
		$chardata['stats']['mainHandSpeed'] = (string) $xml->characterInfo->characterTab->melee->mainHandDamage['speed'];
		$chardata['stats']['mainHandDps'] = (string) $xml->characterInfo->characterTab->melee->mainHandDamage['dps'];
		$chardata['stats']['mainHandExpertise'] = (string) $xml->characterInfo->characterTab->melee->expertise['value'];
		
		$chardata['stats']['offHandDmgMin'] = (string) $xml->characterInfo->characterTab->melee->offHandDamage['min'];
		$chardata['stats']['offHandDmgMax'] = (string) $xml->characterInfo->characterTab->melee->offHandDamage['max'];
		$chardata['stats']['offHandSpeed'] = (string) $xml->characterInfo->characterTab->melee->offHandDamage['speed'];
		$chardata['stats']['offHandDps'] = 	(string) $xml->characterInfo->characterTab->melee->offHandDamage['dps'];
		$chardata['stats']['offHandExpertise'] = (string) $xml->characterInfo->characterTab->melee->offHandDamage['value'];
		
		$chardata['stats']['attackPower'] = (string) $xml->characterInfo->characterTab->melee->power['effective'];
		$chardata['stats']['hasteRating'] = (string) $xml->characterInfo->characterTab->spell->hasteRating['hasteRating']; //not working
		$chardata['stats']['crit'] = (string) $xml->characterInfo->characterTab->melee->critChance['percent'];
		$chardata['stats']['hitPercent'] = (string) $xml->characterInfo->characterTab->melee->hitRating['increasedHitPercent'];
		$chardata['stats']['arpPercent'] = (string) $xml->characterInfo->characterTab->melee->hitRating['reducedArmorPercent']; //buggy @ castle

		//ranged
		$chardata['stats']['rangedDmgMin'] = (string) $xml->characterInfo->characterTab->ranged->damage['min'];
		$chardata['stats']['rangedDmgMax'] = (string) $xml->characterInfo->characterTab->ranged->damage['max'];
		$chardata['stats']['rangedSpeed'] = (string) $xml->characterInfo->characterTab->ranged->damage['speed'];
		$chardata['stats']['rangedDps'] = (string) $xml->characterInfo->characterTab->ranged->damage['dps'];
		$chardata['stats']['rangedExpertise'] = (string) $xml->characterInfo->characterTab->melee->expertise['value'];
		$chardata['stats']['rangedCrit'] = (string) $xml->characterInfo->characterTab->ranged->critChance['percent'];
		$chardata['stats']['rangedHitRating'] = (string) $xml->characterInfo->characterTab->ranged->hitRating['percent'];
		$chardata['stats']['rangedAttackPower'] = (string) $xml->characterInfo->characterTab->ranged->power['effective'];
		
		//caster
		$chardata['stats']['spellPower'] = (string) $xml->characterInfo->characterTab->spell->bonusDamage->holy['value'];
		$chardata['stats']['spellPen'] = (string) $xml->characterInfo->characterTab->spell->penetration['value'];
		$chardata['stats']['spellCrit'] = (string) $xml->characterInfo->characterTab->spell->critChance->holy['percent'];
		$chardata['stats']['spellCritRating'] = (string) $xml->characterInfo->characterTab->spell->critChance['rating'];
		$chardata['stats']['spellHitPercent'] = (string) $xml->characterInfo->characterTab->spell->hitRating['increasedHitPercent'];
		$chardata['stats']['spellHitRating'] = (string) $xml->characterInfo->characterTab->spell->hitRating['value'];
		$chardata['stats']['mana5'] = (string) $xml->characterInfo->characterTab->spell->manaRegen['notCasting'];
		$chardata['stats']['mana5Combat'] = (string) $xml->characterInfo->characterTab->spell->manaRegen['casting'];
		
		//def
		$chardata['stats']['armor'] = (string) $xml->characterInfo->characterTab->defenses->armor['base'];
		$chardata['stats']['dodge'] = (string) $xml->characterInfo->characterTab->defenses->dodge['percent'];
		$chardata['stats']['dodgeRating'] = (string) $xml->characterInfo->characterTab->defenses->dodge['rating'];
		$chardata['stats']['parry'] = (string) $xml->characterInfo->characterTab->defenses->parry['percent'];
		$chardata['stats']['parryRating'] = (string) $xml->characterInfo->characterTab->defenses->parry['rating'];
		$chardata['stats']['block'] = (string) $xml->characterInfo->characterTab->defenses->block['percent'];
		$chardata['stats']['blockRating'] = (string) $xml->characterInfo->characterTab->defenses->block['rating'];
		$chardata['stats']['pvpResilienceRating'] = (string) $xml->characterInfo->characterTab->defenses->resilience['value'];
		
		$chardata['stats']['mastery'] = null; //cata
		$chardata['stats']['masteryRating'] = null; //cata
		$chardata['stats']['pvpPower'] = null;//mop
		$chardata['stats']['pvpPowerRating'] = null;//mop
		$chardata['stats']['pvpPowerDamage'] = null;//mop
		$chardata['stats']['pvpPowerHealing'] = null;//mop

		$chardata['professions'] = array();
		$chardata['professions']["primary"] = array();
		for($i=0;$i<4;$i++) 
		{
			$chardata['professions']["primary"][$i]["id"] = (string) $xml->characterInfo->characterTab->professions->skill[$i]['id'];
			$chardata['professions']["primary"][$i]["name"] = (string) $xml->characterInfo->characterTab->professions->skill[$i]['name'];
			$key = (string) $xml->characterInfo->characterTab->professions->skill[$i]['key'] ;
			$chardata['professions']["primary"][$i]["icon"] = (string) $this->convert['professionIcons']["$key"];
			$chardata['professions']["primary"][$i]["rank"] =(string) $xml->characterInfo->characterTab->professions->skill[$i]['value'];
			$chardata['professions']["primary"][$i]["max"] = (string) $xml->characterInfo->characterTab->professions->skill[$i]['max'];
			$chardata['professions']["primary"][$i]["recipes"] = array();
		}
		/*
		$chardata['professions']["secondary"]["0"]["id"] = "NULL";
		$chardata['professions']["secondary"]["0"]["name"] = "NULL";
		$chardata['professions']["secondary"]["0"]["icon"] = "NULL";
		$chardata['professions']["secondary"]["0"]["rank"] = "NULL";
		$chardata['professions']["secondary"]["0"]["max"] = "NULL";
		$chardata['professions']["secondary"]["0"]["recipes"] = array();
		*/

		$chardata['reputation'] = array();
		$chardata['titles'] = array();
		$chardata['achievements'] = array();
		$chardata['achievements']['achievementsCompleted']  = array();
		$chardata['achievements']['achievementsCompletedTimestamp']  = array();
		$chardata['achievements']['criteria']  = array();
		$chardata['achievements']['criteriaQuantity']  = array();
		$chardata['achievements']['criteriaTimestamp']  = array();
		$chardata['achievements']['criteriaCreated']  = array();

		// talents, glyphs
        /*        $url = 'http://armory.wow-castle.de/character-talents.xml?r=WoW-Castle+PvE&cn='.$user;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Language: de-de, de;"));
                $talent_content = curl_exec ($ch);
                curl_close ($ch);
                $talent_xml = new SimpleXMLElement($talent_content);
		*/
		$chardata['talents'] = array();
		for($i=0;$i<2;$i++)
		{
			$chardata['talents'][$i] = array();
			$chardata['talents'][$i]['talents'] = array();
			$chardata['talents'][$i]['glyphs'] = array();
			//	foreach($talent_xml->characterInfo->talents->talentGroup[$i]->glyphs->glyph as $glyph)
			//		$chardata['talents'][$i]['glyphs'][utf8_decode($glyph['type'])][] = array("item" => NULL, "glyph" => $glyph['glyph'], "name" => $glyph['name'], "icon" => NULL);
			$chardata['talents'][$i]['spec'] = array();
			$chardata['talents'][$i]['spec']['name'] = $xml->characterInfo->characterTab->talentSpecs->talentSpec[$i]['prim'];
			$chardata['talents'][$i]['spec']['role'] = "NULL";
			$chardata['talents'][$i]['spec']['backgroundImage'] = "NULL";
			$chardata['talents'][$i]['spec']['icon'] = $xml->characterInfo->characterTab->talentSpecs->talentSpec[$i]['icon'];
			$chardata['talents'][$i]['spec']['description'] = null;
			$chardata['talents'][$i]['spec']['order'] = null;
			$chardata['talents'][$i]['calcTalent'] = null;
			$chardata['talents'][$i]['calcSpec'] = null;
			$chardata['talents'][$i]['calcGlyph'] = null;
		}
		
		$chardata['appearance'] = array();
		$chardata['mounts'] = array();
		$chardata['pets'] = array();
		$chardata['petSlots'] = array();
		$chardata['progression'] = array();
		$chardata['pvp'] = array();
		$chardata['quests'] = array();
		$chardata['pvp'] = array();
		$chardata['pvp']['ratedBattlegrounds'] = array();
		$chardata['pvp']['arenaTeams'] = array();
		$chardata['pvp']['totalHonorableKills'] = "Null";
		$chardata['achievement'] = array();
		print_r ($errorchk);
		return $chardata;
		return (!$errorchk) ? $chardata: $errorchk;
	}

	/**
	* Create full character Icon Link
	* 
	* @param $thumb		Thumbinformation returned by battlenet JSON feed
	* @return string
	*/
	public function characterIcon($chardata, $forceUpdateAll = false){
		$cached_img	= str_replace('/', '_', 'image_character_'.$this->_config['serverloc'].'_'.$chardata['thumbnail']);
		$img_charicon	= $this->get_CachedData($cached_img, false, true);
		if(!$img_charicon && ($forceUpdateAll || ($this->chariconUpdates < $this->_config['maxChariconUpdates']))){
			$this->set_CachedData($this->read_url($this->_config['apiRenderUrl'].sprintf('%s/%s', $this->_config['serverloc'], $chardata['thumbnail'])), $cached_img, true);
			$img_charicon	= $this->get_CachedData($cached_img, false, true);

			// this is due to an api bug and may be removed some day, thumbs are always set and could be 404!
			if(filesize($img_charicon) < 400){
				$linkprfx	= str_replace('/api', '/wow/static/images/2d/avatar/', $this->_config['apiUrl']);
				$this->set_CachedData($this->read_url($linkprfx.sprintf('%s-%s.jpg', $chardata['race'], $chardata['gender'])), $cached_img, true);
			}
			$this->chariconUpdates++;
		}
		
		if (!$img_charicon){
			$img_charicon	= $this->get_CachedData($cached_img, false, true, true);
			if(filesize($img_charicon) < 400){
				$img_charicon = '';
			}
		}
		
		return $img_charicon;
	}

	public function characterIconSimple($race = '1', $gender='0'){
	return 'http://eu.battle.net/wow/static/images/2d/profilemain/race/3-1.jpg';
		return sprintf('http://eu.battle.net/wow/static/images/2d/profilemain/race/%s-%s.jpg', $race, $gender);
	}

	/**
	* Create full character Image Link
	* 
	* @param $thumb		Thumbinformation returned by battlenet JSON feed
	* @param $type		Image tyoe, big or inset
	* @return string
	*/
	public function characterImage($chardata, $type='big', $forceUpdateAll = false){
		switch($type){
			case 'big':		$dtype_ending = 'profilemain'; break;
			case 'inset':	$dtype_ending = 'inset'; break;
			default: $dtype_ending = 'profilemain';
		}
		$imgfile = str_replace('avatar.jpg', $dtype_ending.'.jpg', $chardata['thumbnail']);
		$cached_img	= str_replace('/', '_', 'image_big_character_'.$this->_config['serverloc'].'_'.$imgfile);
		$img_charicon	= $this->get_CachedData($cached_img, false, true);
		if(!$img_charicon || $forceUpdateAll){
			$this->set_CachedData($this->read_url($this->_config['apiRenderUrl'].sprintf('%s/%s', $this->_config['serverloc'], $imgfile)), $cached_img, true);
			$img_charicon	= $this->get_CachedData($cached_img, false, true);
		}
		return 'http://eu.battle.net/wow/static/images/2d/profilemain/race/1-0.jpg';
		return $img_charicon;
	}

	public function talentIcon($name){
		return 'http://'.$this->_config['serverloc'].'.media.blizzard.com/wow/icons/36/'.$name.'.jpg';
	}

	public function selectedTitle($titles, $cleantitle=false){
		if(is_array($titles)){
			foreach($titles as $titledata){
				if(isset($titledata['selected']) && $titledata['selected'] == '1'){
					if($cleantitle){
						$temp_data = str_replace('%s, ', '', $titledata['name']);
						$temp_data = str_replace(' %s', '', $temp_data);
						$temp_data = str_replace('%s ', '', $temp_data);
						$temp_data = str_replace('%s', '', $temp_data);
						return $temp_data;
					}else{
						return $titledata['name'];
					}
				}
			}
		}
	}

	/**
	* Fetch guild information
	* 
	* @param $user		Character Name
	* @param $realm		Realm Name
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function guild($guild, $realm, $force=false){
		$realm	= $this->ConvertInput($this->cleanServername($realm));
		$guild	= $this->ConvertInput($guild);
		$wowurl	= $this->_config['apiUrl'].sprintf('wow/guild/%s/%s?locale=%s&fields=members,achievements,news,challenge', $realm, $guild, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('guilddata_'.$guild.$realm, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'guilddata_'.$guild.$realm);
		}
		$chardata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($chardata);
		return (!$errorchk) ? $chardata: $errorchk;
	}

	/**
	* Generate guild tabard & save in cache
	* 
	* @param $emblemdata	emblem data array of battle.net api
	* @param $faction		name of the faction
	* @param $guild			name of the guild
	* @param $imgwidth		width of the image
	* @return bol
	*/
	public function guildTabard($emblemdata, $faction, $guild, $imgwidth=215){
		$cached_img	= sprintf('image_tabard_%s_w%s.png', strtolower(str_replace(' ', '', $guild)), $imgwidth);
		if(!$imgfile = $this->get_CachedData($cached_img, false, true)){
			if(!function_exists('imagecreatefrompng') || !function_exists('imagelayereffect') || version_compare(PHP_VERSION, "5.3.0", '<')){
				return $this->root_path.sprintf('games/wow/guild/tabard_%s.png', (($faction == 0) ? 'alliance' : 'horde'));
			}
			$imgfile	= $this->get_CachedData($cached_img, false, true, true);

			// set the URL of the required image parts
			$img_emblem		= $this->_config['apiTabardRenderUrl'].sprintf('emblem_%02s', $emblemdata['icon']) .'.png';
			$img_border		= $this->_config['apiTabardRenderUrl']."border_".(($emblemdata['border'] == '-1') ? sprintf("%02s", $emblemdata['border']) : '00').".png";
			$img_ring		= $this->_config['apiTabardRenderUrl'].sprintf('ring-%s', (($faction == 0) ? 'alliance' : 'horde')) .'.png';
			$img_background	= $this->_config['apiTabardRenderUrl'].'bg_00.png';
			$img_shadow		= $this->_config['apiTabardRenderUrl'].'shadow_00.png';
			$img_overlay	= $this->_config['apiTabardRenderUrl'].'overlay_00.png';
			$img_hooks		= $this->_config['apiTabardRenderUrl'].'hooks.png';

			// set the image size (max width 215px) & generate the guild tabard image
			$img_resampled	= false;
			if ($imgwidth > 1 && $imgwidth < 215){
				$img_resampled	= true;
				$imgheight		= ($imgwidth/215)*230;
				$img_tabard		= imagecreatetruecolor($imgwidth, $imgheight);
				$tranparency	= imagecolorallocatealpha($img_tabard, 0, 0, 0, 127);
				imagefill($img_tabard, 0, 0, $tranparency);
				imagesavealpha($img_tabard,true);
				imagealphablending($img_tabard, true);
			}

			// generate the output image
			$img_genoutput	= imagecreatetruecolor(215, 230);
			imagesavealpha($img_genoutput,true);
			imagealphablending($img_genoutput, true);
			$tranparency	= imagecolorallocatealpha($img_genoutput, 0, 0, 0, 127);
			imagefill($img_genoutput, 0, 0, $tranparency);

			// generate the ring
			$ring			= imagecreatefrompng($img_ring);
			$ring_size		= getimagesize($img_ring);
			$emblem_image	= imagecreatefrompng($img_emblem);
			$emblem_size	= getimagesize($img_emblem);
			imagelayereffect($emblem_image, IMG_EFFECT_OVERLAY);
			$tmp_emblemcolor= preg_replace('/^ff/i','',$emblemdata['iconColor']);
			$emblemcolor	= array(hexdec(substr($tmp_emblemcolor,0,2)), hexdec(substr($tmp_emblemcolor,2,2)), hexdec(substr($tmp_emblemcolor,4,2)));
			imagefilledrectangle($emblem_image,0,0,$emblem_size[0],$emblem_size[1],imagecolorallocate($emblem_image, $emblemcolor[0], $emblemcolor[1], $emblemcolor[2]));

			// generate the border
			$border			= imagecreatefrompng($img_border);
			$border_size	= getimagesize($img_border);
			imagelayereffect($border, IMG_EFFECT_OVERLAY);
			$tmp_bcolor		= preg_replace('/^ff/i','',$emblemdata['borderColor']);
			$bordercolor	= array(hexdec(substr($tmp_bcolor,0,2)), hexdec(substr($tmp_bcolor,2,2)), hexdec(substr($tmp_bcolor,4,2)));
			imagefilledrectangle($border,0,0,$border_size[0]+100,$border_size[0]+100,imagecolorallocate($border, $bordercolor[0], $bordercolor[1], $bordercolor[2]));

			// generate the background
			$shadow			= imagecreatefrompng($img_shadow);
			$bg				= imagecreatefrompng($img_background);
			$bg_size		= getimagesize($img_background);
			imagelayereffect($bg, IMG_EFFECT_OVERLAY);
			$tmp_bgcolor	= preg_replace('/^ff/i','',$emblemdata['backgroundColor']);
			$bgcolor		= array(hexdec(substr($tmp_bgcolor,0,2)), hexdec(substr($tmp_bgcolor,2,2)), hexdec(substr($tmp_bgcolor,4,2)));
			imagefilledrectangle($bg,0,0,$bg_size[0]+100,$bg_size[0]+100,imagecolorallocate($bg, $bgcolor[0], $bgcolor[1], $bgcolor[2]));

			// put it together...
			imagecopy($img_genoutput,$ring,0,0,0,0, $ring_size[0],$ring_size[1]);
			$size			= getimagesize($img_shadow);
			imagecopy($img_genoutput,$shadow,20,23,0,0, $size[0],$size[1]);
			imagecopy($img_genoutput,$bg,20,23,0,0, $bg_size[0],$bg_size[1]);
			imagecopy($img_genoutput,$emblem_image,37,53,0,0, $emblem_size[0],$emblem_size[1]);
			imagecopy($img_genoutput,$border,32,38,0,0, $border_size[0],$border_size[1]);
			$size			= getimagesize($img_overlay);
			imagecopy($img_genoutput,imagecreatefrompng($img_overlay),20,25,0,0, $size[0],$size[1]);
			$size			= getimagesize($img_hooks);
			imagecopy($img_genoutput,imagecreatefrompng($img_hooks),18,23,0,0, $size[0],$size[1]);

			// check if the image is the same size as the image file parts, if not, resample the image
			if ($img_resampled){
				imagecopyresampled($img_tabard, $img_genoutput, 0, 0, 0, 0, $imgwidth, $imgheight, 215, 230);
			}else{
				$img_tabard = $img_genoutput;
			}
			
			$strTmpFolder = (is_object($this->pfh)) ? $this->pfh->FolderPath('tmp', '').$cached_img : $imgfile;
			
			//Create PNG
			imagepng($img_tabard,$strTmpFolder);
			
			//Move from tmp-Folder to right folder
			if (is_object($this->pfh)){
				$this->pfh->FileMove($strTmpFolder, $imgfile);
			}
		}
		return $imgfile;
	}

	/**
	* Fetch realm information
	* 
	* @param $realm		Realm Name
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function realm($realms, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/realm/status?locale=%s&realms=%s', $this->_config['locale'], $realms = ((is_array($realms)) ? implode(",",$realms) : ''));
		if(!$json	= $this->get_CachedData('realmdata_'.str_replace(",", "", $realms), $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'realmdata_'.str_replace(",", "", $realms));
		}
		$realmdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($realmdata);
		return (!$errorchk) ? $realmdata: $errorchk;
	}

	/**
	* Fetch pvpteam information
	* 
	* @param $realm		Realm Name
	* @param $teamname	Team name
	* @param $teamsize	TeamSize = "2v2" | "3v3" | "5v5"
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function pvpteam($realm, $teamname, $teamsize, $force=false){
		switch($teamname){
			case '2v2':	$teamsize = '2v2'; break;
			case '3v3':	$teamsize = '3v3'; break;
			case '5v5':	$teamsize = '5v5'; break;
			default: $teamsize = '2v2';
		}
		$wowurl = $this->_config['apiUrl'].sprintf('wow/arena/%s/%s/%s?locale=%s', $this->ConvertInput($realm), $teamsize, $this->ConvertInput($teamname), $this->_config['locale']);
		if(!$json	= $this->get_CachedData('pvpdata_'.$guild.$teamname.$teamsize, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'pvpdata_'.$guild.$teamname.$teamsize);
		}
		$pvpdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($pvpdata);
		return (!$errorchk) ? $pvpdata: $errorchk;
	}

	/**
	* Fetch item information
	* 
	* @param $itemid	battlenet Item ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function item($itemid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/item/%s?locale=%s', $itemid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('itemdata_'.$itemid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'itemdata_'.$itemid);
		}
		$itemdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($itemdata);
		return (!$errorchk) ? $itemdata: $errorchk;
	}
	
	/**
	* Fetch achievement information
	* 
	* @param $achievementid		battlenet Achievement ID
	* @param $force				Force the cache to update?
	* @return bol
	*/
	public function achievement($achievementid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/achievement/%s?locale=%s', $achievementid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('achievementdata_'.$achievementid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'achievementdata_'.$achievementid);
		}
		$achievementdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($achievementdata);
		return (!$errorchk) ? $achievementdata : $errorchk;
	}


	/**
	* Fetch quest information
	* 
	* @param $questid	battlenet quest ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function quest($questid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/quest/%s?locale=%s', $questid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('questdatadata_'.$questid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'questdatadata_'.$questid);
		}
		$questdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($questdata);
		return (!$errorchk) ? $questdata : $errorchk;
	}

	/**
	* Fetch recipe information
	* 
	* @param $questid	battlenet quest ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function recipe($recipeid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/recipe/%s?locale=%s', $recipeid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('recipedatadata_'.$recipeid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'recipedatadata_'.$recipeid);
		}
		$recipe	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($recipe);
		return (!$errorchk) ? $recipe : $errorchk;
	}

	/**
	* Fetch spell information
	* 
	* @param $questid	battlenet quest ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function spell($spellid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/spell/%s?locale=%s', $spellid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('spelldatadata_'.$spellid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'spelldatadata_'.$spellid);
		}
		$spell		= json_decode($json, true);
		$errorchk	= $this->CheckIfError($spell);
		return (!$errorchk) ? $spell : $errorchk;
	}

	/**
	* Fetch challenge mode information
	* 
	* @param $realm		battlenet realm
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function challenge($realm, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/challenge/%s?locale=%s', $this->ConvertInput($realm), $this->_config['locale']);
		if(!$json	= $this->get_CachedData('challengedatadata_'.$realm, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'challengedatadata_'.$realm);
		}
		$challengedata	= json_decode($json, true);
		$errorchk		= $this->CheckIfError($challengedata);
		return (!$errorchk) ? $challengedata : $errorchk;
	}

	/**
	* Fetch challenge mode information
	* 
	* @param $abilityid	Ability ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function battlepet($abilityid, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('wow/battlePet/ability/%s?locale=%s', $abilityid, $this->_config['locale']);
		if(!$json	= $this->get_CachedData('battlepetdatadata_'.$abilityid, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'battlepetdatadata_'.$abilityid);
		}
		$battlepet	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($battlepet);
		return (!$errorchk) ? $battlepet : $errorchk;
	}

	/**
	* This API resource provides a per-realm list of recently generated auction house data dumps.
	* 
	* @param $abilityid	Ability ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function auction($realm, $force=false){
		$wowurl = $this->_config['apiUrl'].sprintf('api/wow/auction/data/%s?locale=%s', $this->ConvertInput($realm), $this->_config['locale']);
		if(!$json	= $this->get_CachedData('auctiondatadata_'.$realm, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'auctiondatadata_'.$realm);
		}
		$auction	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($auction);
		return (!$errorchk) ? $auction : $errorchk;
	}

	// DATA RESOURCES
	public function getdata($type='character', $sub_type='achievements', $force=false){
		$wowurl	= $this->_config['apiUrl'].sprintf('wow/data/'.$type.'/'.$sub_type.'?locale=%s', $this->_config['locale']);
		if(!$json	= $this->get_CachedData('data_'.$type.'_'.$sub_type, $force)){
			$json	= $this->read_url($wowurl);
			$this->set_CachedData($json, 'data_'.$type.'_'.$sub_type);
		}
		$chardata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($chardata);
		return (!$errorchk) ? $chardata: $errorchk;
	}
	
	/**
	 * Returns Category for Achievement-ID, usefull for Armory-Links
	 *
	 * @int 	$intAchievID					Armory Achievement-ID
	 * @array 	$arrAchievementData	Difference  Achievement-Data, e.g. from Armory Resources
	 * @return 	formatted String				10588 or 10589:92
	 */
	function getCategoryForAchievement($intAchievID, $arrAchievementData){
		foreach($arrAchievementData['achievements'] as $arrAchievs){
			$intCatID = $arrAchievs['id'];
			foreach ($arrAchievs['achievements'] as $arrAchievs2){
				if ((int)$arrAchievs2['id'] == $intAchievID) return $intCatID;
			}
			
			if (isset($arrAchievs['categories'])){
				foreach ($arrAchievs['categories'] as $arrCatAchievs2){
					$intNewCatID = $intCatID . ':'. $arrCatAchievs2['id'];
					foreach ($arrCatAchievs2['achievements'] as $arrCatAchievs3){
						if ((int)$arrCatAchievs3['id'] == $intAchievID) return $intNewCatID;
					}
				}
			}
		}
	}

	/**
	* Check if the JSON is an error result
	* 
	* @param $data		XML Data of Char
	* @return error code
	*/
	protected function CheckIfError($data){
		$status	= (isset($data['status'])) ? $data['status'] : false;
		$reason	= (isset($data['reason'])) ? $data['reason'] : false;
		$error = '';
		if($status){
			return array('status'=>$status,'reason'=>$reason);
		}else{
			return false;
		}
	}

	/**
	* Clean the Servername if taken from Database
	* 
	* @return string output
	*/
	public function cleanServername($server){
		return html_entity_decode($server,ENT_QUOTES,"UTF-8");
	}

	/**
	* Convert from Armory ID to EQDKP Id or reverse
	* 
	* @param $name			name/id to convert
	* @param $type			int/string?
	* @param $cat			category (classes, races, months)
	* @param $ssw			if set, convert from eqdkp id to armory id
	* @return string/int output
	*/
	public function ConvertID($name, $type, $cat, $ssw=false){
		if($ssw){
			if(!is_array($this->converts[$cat])){
				$this->converts[$cat] = array_flip($this->convert[$cat]);
			}
			return ($type == 'int') ? $this->converts[$cat][(int) $name] : $this->converts[$cat][$name];
		}else{
			return ($type == 'int') ? $this->convert[$cat][(int) $name] : $this->convert[$cat][$name];
		}
	}

	/**
	* Convert talent from icon to id
	* 
	* @param $name			name/id to convert
	* @return string/int output
	*/
	public function ConvertTalent($name){
		return key(search_in_array($name, $this->convert['talent']));
	}

	/**
	* Prepare a string for beeing sent to armory
	* 
	* @param $input 
	* @return string output
	*/
	public function ConvertInput($input, $removeslash=false, $removespace=false){
		// new servername convention: mal'ganis = malganis
		$input = ($removespace) ? str_replace(" ", "-", $input) : $input;
		return ($removeslash) ? stripslashes(str_replace("'", "", $input)) : stripslashes(rawurlencode($input));
	}

	/**
	* Write JSON to Cache
	* 
	* @param	$json		XML string
	* @param	$filename	filename of the cache file
	* @return --
	*/
	protected function set_CachedData($json, $filename, $binary=false){
		if($this->_config['caching']){
			$cachinglink = $this->binaryORdata($filename, $binary);
			if(is_object($this->pfh)){
				$this->pfh->putContent($this->pfh->FolderPath('armory', 'cache', false).$cachinglink, $json);
			}else{
				file_put_contents('data/'.$cachinglink, $json);
			}
		}
	}

	/**
	* get the cached JSON if not outdated & available
	* 
	* @param	$filename	filename of the cache file
	* @param	$force		force an update of the cached json file
	* @return --
	*/
	protected function get_CachedData($filename, $force=false, $binary=false, $returniffalse=false){
		if(!$this->_config['caching']){return false;}
		$data_ctrl = false;
		$rfilename	= (is_object($this->pfh)) ? $this->pfh->FolderPath('armory', 'cache').$this->binaryORdata($filename, $binary) : 'data/'.$this->binaryORdata($filename, $binary);
		if(is_file($rfilename)){
			$data_ctrl	= (!$force && (filemtime($rfilename)+(3600*$this->_config['caching_time'])) > time()) ? true : false;
		}
		return ($data_ctrl || $returniffalse) ? (($binary) ? $rfilename : @file_get_contents($rfilename)) : false;
	}

	/**
	* delete the cached data
	* 
	* @return --
	*/
	public function DeleteCache(){
		if(!$this->_config['caching']){return false;}
		$rfoldername	= (is_object($this->pfh)) ? $this->pfh->FolderPath('armory', 'cache') : 'data/';
		return $this->pfh->Delete($rfoldername);
	}

	/**
	* check if binary files or json/data
	* 
	* @param	$input	the input
	* @param	$binary	true/false
	* @return --
	*/
	protected function binaryORdata($input, $binary=false){
		return ($binary) ? $input : 'data_'.$this->_config['locale'].md5($input);
	}

	/**
	* set the API Url
	* 
	* @param	$serverloc	the location of the server
	* @return --
	*/
	protected function setApiUrl($serverloc){
		$this->_config['apiUrl']				= str_replace('{region}', $serverloc, self::apiurl);
		$this->_config['apiRenderUrl']			= str_replace('{region}', $serverloc, self::staticrenderurl);
		$this->_config['apiTabardRenderUrl']	= str_replace('{region}', $serverloc, self::tabardrenderurl);
	}

	/**
	* Fetch the Data from URL
	* 
	* @param $url URL to Download
	* @return json
	*/
	protected function read_url($url) {
		$apikeyhead = (isset($this->_config['apiKeyPrivate']) && isset($this->_config['apiKeyPublic']) && $this->_config['apiKeyPrivate'] != '' && $this->_config['apiKeyPublic'] != '') ? $this->gen_api_header($url) : '';
		if(!is_object($this->puf)) {
			global $eqdkp_root_path;
			include_once($eqdkp_root_path.'core/urlfetcher.class.php');
			$this->puf = new urlfetcher();
		}
		return $this->puf->fetch($url, $apikeyhead);
	}

	private function gen_api_header($url){
		$date = date(DATE_RFC2822);
		$headers = array(
			'Date: '. $date,
			'Authorization: BNET '. $this->_config['apiKeyPublic'] .':'. base64_encode(hash_hmac('sha1', "GET\n{$date}\n{$url}\n", $this->_config['apiKeyPrivate'], true))
		);
		return $headers;
	}

	/**
	* Check if an error occured
	* 
	* @return error
	*/
	public function CheckError(){
		return ($this->error) ? $this->error : false;
	}
}
?>