<?php
/*
* Project:     EQdkp-Plus Raidlogimport
* License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:       2009
* Date:        $Date: 2013-02-07 15:23:51 +0100 (Thu, 07 Feb 2013) $
* -----------------------------------------------------------------------
* @author      $Author: hoofy_leon $
* @copyright   2008-2009 hoofy_leon
* @link        http://eqdkp-plus.com
* @package     raidlogimport
* @version     $Rev: 13027 $
*
* $Id: rli_parse.class.php 13027 2013-02-07 14:23:51Z hoofy_leon $
*/

if(!defined('EQDKP_INC'))
{
	header('HTTP/1.0 Not Found');
	exit;
}

if(!class_exists('rli_parse')) {
class rli_parse extends gen_class {
	public static $shortcuts = array('rli', 'in', 'config', 'user',
		'adj'		=> 'rli_adjustment',
		'item'		=> 'rli_item',
		'member'	=> 'rli_member',
		'raid'		=> 'rli_raid',
	);

	private $toload = array();

	/**
	*	checks wether all nodes are available (if not optional) and complete.
	*	returns an array(1 => bool, 2 => array( contains strings of missing/wrong nodes ))
	*	params: xml => xml to check
	*			xml_form => array, which describes the xml: array(node => array(node => ''));
	*					if prefix is "optional:", the node is only checked for completion
	*					if prefix is "multiple:", all occuring nodes are checked
	*/
	public function check_xml_format($xml, $xml_form, $back=array(1 => true), $pre='') {
		foreach($xml_form as $name => $val) {
			$optional = false;
			if(strpos($name, 'optional:') !== false) {
				$name = str_replace('optional:', '', $name);
				$optional = true;
			}
			$multiple = false;
			if(strpos($name, 'multiple:') !== false) {
				$name = str_replace('multiple:', '', $name);
				$multiple = true;
			}
			if($multiple) {
				$pre .= $name.'->';
				foreach($val as $nname => $vval) {
					$optional = false;
					if(strpos($nname, 'optional:') !== false) {
						$nname = str_replace('optional:', '', $nname);
						$optional = true;
					}
					if((!isset($xml->$name->$nname)) AND !$optional) {
						$back[1] = false;
						$back[2][] = $pre.$nname;
					} else {
						if(isset($xml->$name)) {
							if(is_array($vval)) {
								foreach($xml->$name->children() as $child) {
									$back = $this->check_xml_format($child, $vval, $back, $pre);
								}
								$pre = substr($pre, 0, -(strlen($nname)+2));
							} else {
								foreach($xml->$name->children() as $child) {
									if((!isset($child) OR trim($child) == '') AND !$optional) {
										$back[1] = false;
										$back[2][] = $pre.$name;
									}
								}
							}
						} else {
							$back[1] = false;
							$back[2][] = $name;
						}
					}
					$pre = '';
				}
			} else {
				if((!isset($xml->$name) OR (trim($xml->$name) == '') AND !is_array($val)) AND !$optional) {
					$back[1] = false;
					$back[2][] = $pre.$name;
				} else {
					if(is_array($val)) {
						$pre .= $name.'->';
						$back = $this->check_xml_format($xml->$name, $val, $back, $pre);
						$pre = '';
					}
				}
			}
			if(strpos((string)$val, 'function:') !== false) {
				$func = str_replace('function:', '', $val);
				$back = call_user_func($func, $xml->name, $back);
			}
		}
		return $back;
	}

	private function check_plus_format($xml) {
		$xml = $xml->raiddata;
		$xml_form = array(
			'multiple:zones' => array(
				'zone' => array(
					'enter'	=> '',
					'leave' => '',
					'name'	=> ''
				)
			),
			'multiple:bosskills' => array(
				'optional:bosskill' => array(
					'name'	=> '',
					'time'	=> ''
				)
			),
			'multiple:members' => array(
				'member' => array(
					'name'	=> '',
					'multiple:times' => array('time' => '')
				)
			),
			'multiple:items' => array(
				'optional:item'	=> array(
					'name'		=> '',
					'time'		=> '',
					'member'	=> ''
				)
			)
		);
		return $this->check_xml_format($xml, $xml_form);
	}

	private function parse_plus_string($xml) {
		if(	(trim($xml->head->gameinfo->game) == 'Runes of Magic' AND $this->config->get('default_game') != 'rom') OR
			(trim($xml->head->gameinfo->game) == 'World of Warcraft' AND $this->config->get('default_game') != 'wow')) {
				message_die($this->user->lang('wrong_game'));
		}
		$lang = trim($xml->head->gameinfo->language);
		#$this->rli->add_data['log_lang'] = substr($lang, 0, 2);
		$xml = $xml->raiddata;
		foreach($xml->zones->children() as $zone) {
			$this->raid->add_zone(trim($zone->name), (int) trim($zone->enter), (int) trim($zone->leave), (int) trim($zone->difficulty));
		}
		foreach($xml->bosskills->children() as $bosskill) {
			$this->raid->add_bosskill(trim($bosskill->name), (int) trim($bosskill->time), (int) trim($bosskill->difficulty));
		}
		foreach($xml->members->children() as $xmember) {
			$name = trim($xmember->name);
			$note = (isset($xmember->note)) ? trim($xmember->note) : '';
			$this->member->add($name, trim($xmember->class), trim($xmember->race), trim($xmember->level), $note);
			foreach($xmember->times->children() as $time) {
				$attrs = $time->attributes();
				$type = $attrs['type'];
				$extra = $attrs['extra'];
				$this->member->add_time($name, $time, $type, $extra);
			}
		}
		foreach($xml->items->children() as $xitem) {
			$cost = (isset($xitem->cost)) ? trim($xitem->cost) : '';
			$id = (isset($xitem->itemid)) ? trim($xitem->itemid) : '';
			$this->item->add(trim(($xitem->name)), trim(($xitem->member)), $cost, (int) $id, (int) trim($xitem->time));
		}
	}

	private function check_eqdkp_format($xml, $magic=false) {
		$back[1] = true;
		if(!isset($xml->start)) {
			$back[1] = false;
			$back[2][] = 'start';
		} else {
			if(!(stristr($xml->start, ':'))) {
				$back[1] = false;
				$back[2][] = 'start in format: MM/DD/YY HH:MM:SS';
			}
		}
		if(!isset($xml->end)) {
			$back[1] = false;
			$back[2][] = 'end';
		} else {
			if(!(stristr($xml->start, ':'))) {
				$back[1] = false;
				$back[2][] = 'end in format: MM/DD/YY HH:MM:SS';
			}
		}
		if(!isset($xml->BossKills)) {
			$back[1] = false;
			$back[2][] = 'BossKills';
		} else {
			foreach($xml->BossKills->children() as $bosskill) {
				if($bosskill) {
					if(!isset($bosskill->name)) {
						$back[1] = false;
						$back[2][] = 'BossKills->name';
					}
					if(!isset($bosskill->time)) {
						$back[1] = false;
						$back[2][] = 'BossKills->time';
					}
				}
			}
		}
		if(!isset($xml->Loot)) {
			$back[1] = false;
			$back[2][] = 'Loot';
		} else {
			foreach($xml->Loot->children() as $loot) {
				if($loot) {
					if(!isset($loot->ItemName)) {
						$back[1] = false;
						$back[2][] = 'Loot->ItemName';
					}
					if(!isset($loot->Player)) {
						$back[1] = false;
						$back[2][] = 'Loot->Player';
					}
					if(!isset($loot->Time)) {
						$back[1] = false;
						$back[2] = 'Loot->Time';
					}
				}
			}
		}
		if(!$magic) {
			if(!isset($xml->PlayerInfos)) {
				$back[1] = false;
				$back[2][] = 'PlayerInfos';
			} else {
				foreach($xml->PlayerInfos->children() as $mem) {
					if(!isset($mem->name)) {
						$back[1] = false;
						$back[2][] = 'PlayerInfos->name';
					}
				}
			}
		}
		if(!isset($xml->Join)) {
			$back[1] = false;
			$back[2][] = 'Join';
		} else {
			foreach($xml->Join->children() as $join) {
				if(!isset($join->player)) {
					$back[1] = false;
					$back[2][] = 'Join->player';
				}
				if(!isset($join->time)) {
					$back[1] = false;
					$back[2][] = 'Join->time';
				}
			}
		}
		if(!isset($xml->Leave)) {
			$back[1] = false;
			$back[2][] = 'Leave';
		} else {
			foreach($xml->Leave->children() as $leave) {
				if(!isset($leave->player)) {
					$back[1] = false;
					$back[2][] = 'Leave->player';
				}
				if(!isset($leave->time)) {
					$back[1] = false;
					$back[2][] = 'Leave->time';
				}
			}
		}
		return $back;
	}

	private function parse_eqdkp_string($xml, $magic=false) {
		$this->raid->add_zone(trim($xml->zone), strtotime($xml->start), strtotime($xml->end), trim($xml->difficulty));
		foreach ($xml->BossKills->children() as $bosskill) {
			$this->raid->add_bosskill(trim($bosskill->name), strtotime($bosskill->time));
		}
		foreach($xml->Loot->children() as $loot) {
			$player = (trim($loot->Player));
			$cost = (array_key_exists('Costs', $loot)) ? (int) $loot->Costs : (int) $loot->Note;
			$this->item->add((trim($loot->ItemName)), $player, $cost, substr(trim($loot->ItemID), 0, 5), strtotime($loot->Time));
		}
		if(!$magic) {
			foreach($xml->PlayerInfos->children() as $xmember) {
				$this->member->add(trim(($xmember->name)), trim(($xmember->class)), trim(($xmember->race)), trim($xmember->level), trim(($xmember->note)));
			}
		}
		foreach ($xml->Join->children() as $joiner) {
			$this->member->add_time((trim($joiner->player)), strtotime($joiner->time), 'join');
		}
		foreach ($xml->Leave->children() as $leaver) {
			$this->member->add_time((trim($leaver->player)), strtotime($leaver->time), 'leave');
		}
	}

	private function parse_magicdkp_string($xml) {
		return $this->parse_eqdkp_string($xml, true);
	}

	private function check_magicdkp_format($xml) {
		return $this->check_eqdkp_format($xml, true);
	}

	public function parse_string($xml) {
		if(method_exists($this, 'parse_'.$this->rli->config('parser').'_string')) {
			$back = call_user_func(array($this, 'check_'.$this->rli->config('parser').'_format'), $xml);
			if($back[1]) {
				$this->raid->flush_data();
				call_user_func(array($this, 'parse_'.$this->rli->config('parser').'_string'), $xml);
				$this->raid->create();
				$this->raid->recalc(true);
				$this->member->finish();
			} else {
				message_die($this->user->lang('wrong_format').' '.$this->user->lang($this->rli->config('parser').'_format').'<br />'.$this->user->lang('rli_miss').implode(', ', $back[2]));
			}
		} else {
			message_die($this->user->lang('no_parser'));
		}
	}
}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_rli_parse', rli_parse::$shortcuts);
?>