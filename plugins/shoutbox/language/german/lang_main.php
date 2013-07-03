<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-09-02 10:09:49 +0200 (Fr, 02. Sep 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: Aderyn $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11183 $
 *
 * $Id: lang_main.php 11183 2011-09-02 08:09:49Z Aderyn $
 */

if (!defined('EQDKP_INC'))
{
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
  'shoutbox'                        => 'Shoutbox',
  'sb_shoutbox'                     => 'Shoutbox',

  // Portal
  'shoutbox_name'                   => 'Shoutbox',
  'shoutbox_desc'                   => 'Shoutbox ist ein Plugin mit dem User kleine Mitteilungen austauschen können.',

  // Description
  'sb_short_desc'                   => 'Shoutbox',
  'sb_long_desc'                    => 'Shoutbox ist ein Plugin mit dem User kleine Mitteilungen austauschen können.',

  // General
  'sb_plugin_not_installed'         => 'Das Shoutbox Plugin ist nicht installiert',
  'sb_php_version'                  => "Shoutbox benötigt PHP %1\$s oder höher. Dein Server läuft mit PHP %2\$s",
  'sb_plus_version'                 => "Shoutbox benötigt EQDKP-PLUS %1\$s oder höher. Die installierte Version ist %2\$s",
  'sb_no_view_permission'			=> "Du hast leider keine Berechtigung, um Shouts zu sehen.",

  // Menu
  'sb_manage_archive'               => 'Archiv Verwalten',

  // Archive
  'sb_written_by'                   => 'geschrieben von',
  'sb_written_at'                   => 'um',

  // Admin
  'sb_delete_success'               => 'Einträge erfolgreich gelöscht',
  'sb_settings_info'                => 'Weitere Einstellungen für die Shoutbox findet Ihr unter den <a href="'.registry::get_const('root_path').'admin/manage_portal.php'.registry::get_const('SID').'">Portalmodul Einstellungen</a>',
  'sb_use_users'                    => 'Benutzernamen anstatt der Charakternamen verwenden',
  'sb_use_users_help'               => 'Beim Ändern von Charakteren zu Benutzern werden die bestehenden Einträge aktualisiert.<br/>Beim Ändern von Benutzern zu Charakteren werden die bestehenden Einträge gelöscht!',
  'sb_convert_member_user_success'  => 'Alle Charaktere in den Einträgen wurden erfolgreich zu Benutzern aktualisiert.',
  'sb_convert_user_member_success'  => 'Alle bestehenden Einträge wurden gelöscht',

  // Configuration
  'sb_config_saved'                 => 'Einstellungen wurden gespeichert',
  'sb_header_general'               => 'Allgemeine Shoutbox Einstellungen',

  // Portal Modules
  'sb_output_count_limit'           => 'Maximale Anzahl an Shoutbox Einträgen.',
  'sb_show_date'                    => 'Zusätzlich das Datum anzeigen?',
  'sb_show_archive'                 => 'Archiv anzeigen?',
  'sb_max_text_length'              => 'Maximal erlaubte Textlänge eines Eintrags',
  'sb_input_box_location'           => 'Position des Eingabefeldes',
  'sb_location_top'                 => 'Oberhalb der Einträge',
  'sb_location_bottom'              => 'Unterhalb der Einträge',
  'sb_autoreload'                   => 'Zeit in Sekunden nach der die Shoutbox automatisch neu geladen werden soll (Standard 0 = Aus)',
  'sb_autoreload_help'              => 'Wird 0 eingetragen so wird das automatische Neu Laden abgeschalten',
  'sb_no_character_assigned'        => 'Es wurde kein Charakter verknüpft. Es muss ein Charakter verknüpft sein bevor Einträge gemacht werden können.',
  'sb_submit_text'                  => 'Absenden',
  'sb_save_wait'                    => 'Speichern, bitte warten...',
  'sb_reload'                       => 'Neu laden',
  'sb_no_entries'                   => 'Keine Einträge',
  'sb_archive'                      => 'Archiv',
  'sb_shoutbox_archive'             => 'Shoutbox Archiv',

  // Exchange
  'sb_missing_char_id'              => 'Es wurde keine gültige Charakter ID angegeben',
  'sb_missing_text'                 => 'Es wurde kein Text angegeben',

  // About/Credits
  'sb_about_header'                 => 'Über Shoutbox',
  'sb_credits_part1'                => 'Shoutbox v',
  'sb_credits_part2'                => ' von Aderyn',
  'sb_copyright'                    => 'Copyright',
);

?>
