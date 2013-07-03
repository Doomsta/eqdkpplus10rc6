<?php
/*
 * Project:     EQdkp guildrequest
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-09-02 10:09:49 +0200 (Fr, 02. Sep 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: Aderyn $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest
 * @version     $Rev: 11183 $
 *
 * $Id: lang_main.php 11183 2011-09-02 08:09:49Z Aderyn $
 */

if (!defined('EQDKP_INC'))
{
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
  'guildrequest'                    => 'GuildRequest',

  // Description
  'guildrequest_short_desc'         => 'GuildRequest',
  'guildrequest_long_desc'          => 'GuildRequest ist ein Plugin mit deren Hilfe man Gildenbewerbungen abwickeln kann.',
  
  'gr_manage_form'					=> 'Formular verwalten',
  'gr_vote'							=> 'Über Bewerbung abstimmen',
  'gr_view'							=> 'Bewerbungen ansehen',
  'gr_add'							=> 'Bewerbung schreiben',
  'gr_internal_comment'				=> 'Internen Kommentar schreiben',
  'gr_comment'						=> 'Öffentlichen Kommentar schreiben',
  
  'gr_plugin_not_installed'			=> 'Das GuildRequest-Plugin ist nicht installiert.',
  'gr_select_options'				=> 'Optionen (1 pro Zeile)',
  'gr_required'						=> 'Verpflichtend',
  'gr_delete_selected_fields'		=> 'Ausgewählte Felder löschen',
  'gr_types'						=> array(
	'Textfeld', 'Textbereich', 'Auswahlfeld', 'Gruppenüberschrift', 'Freitext', 'Checkboxen', 'Radio-Buttons',
  ),
  'gr_add_field'					=> 'Neues Feld hinzufügen',
  'gr_delete_field'					=> 'Feld löschen',
  'gr_default_grouplabel'			=> 'Informationen',
  'gr_personal_information'			=> 'Persönliche Informationen',
  'gr_submit_request'				=> 'Bewerbung absenden',
  'gr_email_help'					=> 'Bitte gib eine gültige Email-Adresse an, da Du an diese Email-Adresse alle Benachrichtigungen zu Deiner Bewerbung erhältst.',
  'gr_activationmail_subject'		=> 'Aktiviere deine Bewerbung',
  'gr_viewlink_subject'				=> 'Deine Bewerbung',
  'gr_request_success'				=> 'Deine Bewerbung wurde erfolgreich gespeichert. Eine Email mit dem Link auf diese Seite wurde an Deine Email-Adresse versendet.',
  'gr_vote'							=> 'Abstimmung',
  'gr_internal_comments'			=> 'Interne Kommentare',
  'gr_newcomment_subject'			=> 'Neuer Kommentar zu Deiner Bewerbung',
  'gr_status'						=> array('neu', 'in Bearbeitung', 'Aufgenommen', 'Abgelehnt'),
  'gr_status_text'					=> 'Deine Bewerbung befindet sich in folgendem Status: <b>%s</b>',
  'gr_vote_button'					=> 'Abstimmen',
  'gr_manage_request'				=> 'Bewerbung verwalten',
  'gr_status_help'					=> 'Der Bewerber bekommt bei einer Statusänderung automatisch eine Email gesendet. Willst Du dieser Email noch etwas hinzufügen, benutze das Eingabefeld dafür.',
  'gr_change_status'				=> 'Status ändern',
  'gr_close'						=> 'Bewerbung schließen',
  'gr_open_request'					=> 'Bewerbung wieder öffnen',
  'gr_closed_subject'				=> 'Deine Bewerbung wurde geschlossen',
  'gr_status_subject'				=> 'Deine Bewerbung: Statusänderung',
  'gr_footer'						=> '%1$s Bewerbungen gefunden / %2$s pro Seite',
  'gr_in_list'						=> 'In Bewerbungsliste anzeigen',
  'gr_confirm_delete_requests'		=> 'Bist du sicher, dass Du die Bewerbungen von %s löschen willst?',
  'gr_delete_selected_requests'		=> 'Ausgewählte Bewerbungen löschen',
  'gr_delete_success'				=> 'Die ausgewählten Bewerbungen wurden erfolgreich gelöscht.',
  'gr_notification'					=> '%s Benachrichtigungen',
  'gr_notification_open'			=> '%s offen',
  'gr_mark_all_as_read'				=> 'Alle Bewerbungen als gelesen markieren',
  'gr_send_notification_mails'		=> 'Benachrichtigungs-Email bei neuer Bewerbung senden',
  'gr_closed'						=> 'Die Bewerbung wurde geschlossen.',
  'gr_notification_subject'			=> 'Neue Bewerbung',
  'gr_jgrowl_notifications'			=> 'PopUp-Benachrichtigungen anzeigen',
);

?>
