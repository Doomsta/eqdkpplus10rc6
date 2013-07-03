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
  'guildrequest_long_desc'          => 'GuildRequest is a plugin for managing guild applications.',
  
  'gr_manage_form'					=> 'Manage Form',
  'gr_vote'							=> 'Vote about application',
  'gr_view'							=> 'View applications',
  'gr_add'							=> 'Write application',
  'gr_internal_comment'				=> 'Write internal comment',
  'gr_comment'						=> 'Write public comment',
  
  'gr_plugin_not_installed'			=> 'The GuildRequest-Plugin is not installed.',
  'gr_select_options'				=> 'Options (1 per line)',
  'gr_required'						=> 'Mandatory',
  'gr_delete_selected_fields'		=> 'Delete selected fields',
  'gr_types'						=> array(
	'Textfield', 'Textarea', 'Dropdown', 'Grouplabel', 'free text', 'Checkboxes', 'Radio-Buttons',
  ),
  'gr_add_field'					=> 'Add new field',
  'gr_delete_field'					=> 'Delete field',
  'gr_default_grouplabel'			=> 'Information',
  'gr_personal_information'			=> 'Personal information',
  'gr_submit_request'				=> 'Submit application',
  'gr_email_help'					=> 'Please provide a valid email-address, because you will get all notifications about your application to the provied email-address.',
  'gr_activationmail_subject'		=> 'Activate your application',
  'gr_viewlink_subject'				=> 'Your application',
  'gr_request_success'				=> 'Your application has been saved successfully. An email with the link to this page was sent to your email-address.',
  'gr_vote'							=> 'Voting',
  'gr_internal_comments'			=> 'Internal comments',
  'gr_newcomment_subject'			=> 'New comment in your application',
  'gr_status'						=> array('new', 'in progress', 'Accepted', 'Rejected'),
  'gr_status_text'					=> 'Your applications has the following status: <b>%s</b>',
  'gr_vote_button'					=> 'Vote',
  'gr_manage_request'				=> 'Manage applications',
  'gr_status_help'					=> 'The applicant gets an automated email on status change. If you want to add something to this email, please use the input field.',
  'gr_change_status'				=> 'Change status',
  'gr_close'						=> 'Close application',
  'gr_open_request'					=> 'Reopen application',
  'gr_closed_subject'				=> 'Your application has been closed',
  'gr_status_subject'				=> 'Your application: Status change',
  'gr_footer'						=> 'found %1$s applications / %2$s per page',
  'gr_in_list'						=> 'Show in application-list',
  'gr_confirm_delete_requests'		=> 'Are you sure you want to delete the applications of %s ?',
  'gr_delete_selected_requests'		=> 'Delete selected applications',
  'gr_delete_success'				=> 'The selected applications have been deleted successfully.',
  'gr_notification'					=> '%s Notifications',
  'gr_notification_open'			=> '%s open',
  'gr_mark_all_as_read'				=> 'Mark all Applications as read',
  'gr_send_notification_mails'		=> 'Send Notification Email on new application',
  'gr_closed'						=> 'This application is closed.',
  'gr_notification_subject'			=> 'New application',
  'gr_jgrowl_notifications'			=> 'Show PopUp Notifications',
);

?>
