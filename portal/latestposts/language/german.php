<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-10-06 12:41:52 +0200 (Sa, 06. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy_leon $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12207 $
 * 
 * $Id: german.php 12207 2012-10-06 10:41:52Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'latestposts'					=> 'Neuste Beiträge',
	'latestposts_name'				=> 'Letzte Forums-Beiträge',
	'latestposts_desc'				=> 'Zeigt die neuesten Beiträge aus deinem Forum an',
	'portal_latestposts_nomodule'	=> 'Kein Forenmodul gewählt. Bitte Moduleinstellungen prüfen!',
	'portal_latestposts_invmodule'	=> 'Ungültiges Forenmodul',
	'portal_latestposts_noselectedboards'	=> 'Es wurden keine Foren zur Anzeige ausgewählt.',
	'pk_latestposts_bbmodule'		=> 'Forenmodul',
	'pk_latestposts_amount'			=> 'Zeige neuste x Einträge',
	'pk_latestposts_dbmode'			=> 'Wähle den Verbindungsmodus zur Datenbank',
	'pk_latestposts_dbmode_h'		=> 'Die Datenbankverbindunsdaten werden nur bei der Einstellung "Andere Datenbank als EQDKP" benötigt',
	'pk_latestposts_dbmode_same'	=> 'Forum in selben DB wie das EQDKP',
	'pk_latestposts_dbmode_new'		=> 'Forum in anderen DB als das EQDKP',
	'pk_latestposts_dbmode_bridge'	=> 'Verwende die Verbindung der Bridge',
	'pk_latestposts_dbname'			=> 'Datenbank',
	'pk_latestposts_dbuser'			=> 'Benutzer',
	'pk_latestposts_dbpassword'		=> 'Passwort',
	'pk_latestposts_dbhost'			=> 'Host',
	'pk_latestposts_dbprefix'		=> 'Prefix',
	'pk_latestposts_url'			=> 'URL zum Forum',
	'pk_latestposts_noentries'		=> 'Keine Einträge vorhanden',
	'pk_latestposts_connerror'		=> 'Fehler in der Verbindung',
	'pk_latestposts_lastpost'		=> 'Letzter Eintrag',
	'pk_latestposts_starter'		=> 'Starter',
	'pk_latestposts_posts'			=> 'Antworten',
	'pk_latestposts_title'			=> 'Titel',
	'pk_latestposts_trimtitle'		=> 'Lange Thementitel nach x Buchstaben beschneiden',
	'pk_latestposts_trimtitle_h'	=> 'Gilt nur für die Anzeige Rechts/Links',
	'pk_latestposts_privateforums_help'	=> 'Trage hier die IDs der Foren für die angezeigt Benutzergruppe ein, durch Kommas getrennt',
	'pk_latestposts_privateforums'	=> 'Black-/Whitelist für Benutzergruppe: ',
	'pk_latestposts_privateforums_dd_help'	=> 'Wähle die Foren für die angezeigte Benutzergruppe aus, auf die die Black-/Whitelist angewendet werden soll',
	'pk_latestposts_linktype'		=> 'Wie sollen die Links zu Beiträgen geöffnet werden?',
	'pk_latestposts_blackwhite'		=> 'Black - oder Whitelists',
	'pk_latestposts_blackwhite_h'	=> 'Eingegebene ForenIDs zulassen (Whitelist) oder Abweisen (Blacklist)',
);
?>