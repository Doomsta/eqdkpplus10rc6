<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-11-17 11:37:37 +0100 (Sa, 17. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: shoorty $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 12468 $
 *
 * $Id: german.php 12468 2012-11-17 10:37:37Z shoorty $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}

// Title
$lang['realmstatus']           = 'Serverstatus';
$lang['realmstatus_name']      = 'Serverstatus';
$lang['realmstatus_desc']      = 'Den aktuellen Status des Servers anzeigen';

//  Settings
$lang['rs_realm']              = 'Liste von Servern';
$lang['rs_realm_help']         = 'Bei mehreren Servern müssen diese durch Komma getrennt angegeben werden.';
$lang['rs_us']                 = 'Handelt es sich um einen US Server?';
$lang['rs_us_help']            = 'Diese Einstellung hat nur Auswirkungen wenn als Spiel RIFT oder WoW eingestellt ist.';
$lang['rs_gd']                 = 'GD Lib erkannt. GD Lib Version verwenden?';
$lang['rs_gd_help']            = 'Diese Einstellung hat nur Auswirkungen wenn als Spiel WoW eingestellt ist.';

// Portal Modul
$lang['rs_no_realmname']       = 'Kein Server angegeben';
$lang['rs_realm_not_found']    = 'Server nicht gefunden';
$lang['rs_game_not_supported'] = 'Der Serverstatus wird für das Spiel nicht unterstützt';
$lang['rs_unknown']            = 'Unbekannt';
$lang['rs_realm_status_error'] = "Fehler beim Ermitteln des Serverstatus für %1\$s";
$lang['rs_loading']            = 'Lade Status...';
$lang['rs_loading_error']      = 'Fehler beim Laden des Status.';

?>
