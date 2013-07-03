<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-09-05 08:35:11 +0200 (Mo, 05. Sep 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: Godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11227 $
 *
 * $Id: lang_main.php 11227 2011-09-05 06:35:11Z Godmod $
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
  'shoutbox_desc'                   => 'Shoutbox is a Plugin users are able to exchange short messages with.',

  // Description
  'sb_short_desc'                   => 'Shoutbox',
  'sb_long_desc'                    => 'Shoutbox is a Plugin users are able to exchange short messages with.',

  // General
  'sb_plugin_not_installed'         => 'Shoutbox Plugin not installed',
  'sb_php_version'                  => "Shoutbox requires PHP %1\$s or higher. Your server runs PHP %2\$s",
  'sb_plus_version'                 => "Shoutbox requires EQDKP-PLUS %1\$s or higher. Your installed Version is %2\$s",
  'sb_no_view_permission'			=> "You don't have the permission to view shouts.",

  // Menu
  'sb_manage_archive'               => 'Manage Archive',

  // Archive
  'sb_written_by'                   => 'written by',
  'sb_written_at'                   => 'at',

  // Admin
  'sb_delete_success'               => 'Successfully deleted entries',
  'sb_settings_info'                => 'Further Shoutbox settings could be found within the <a href="'.registry::get_const('root_path').'admin/manage_portal.php'.registry::get_const('SID').'">Portalmodule settings</a>',
  'sb_use_users'                    => 'Use usernames instead of membernames',
  'sb_use_users_help'               => 'On changing membernames to usernames all entries will be updated.<br/>On changing usernames to membernames all entries will be deleted!',
  'sb_convert_member_user_success'  => 'All membernames within the entries have been successfully updated to usernames.',
  'sb_convert_user_member_success'  => 'All entries were deleted.',

  // Configuration
  'sb_config_saved'                 => 'Settings saved successfully',
  'sb_header_general'               => 'General Shoutbox settings',

  // Portal Modules
  'sb_output_count_limit'           => 'Limit of shoutbox entries.',
  'sb_show_date'                    => 'Show date also?',
  'sb_show_archive'                 => 'Show Archive?',
  'sb_max_text_length'              => 'Maximum length of a text entry',
  'sb_input_box_location'           => 'Location of input box',
  'sb_location_top'                 => 'Above entries',
  'sb_location_bottom'              => 'Below entries',
  'sb_autoreload'                   => 'Time in seconds to wait for automatic reload of Shoutbox (Default 0 = Off)',
  'sb_autoreload_help'              => 'Set to 0 to disable automatic reload',
  'sb_no_character_assigned'        => 'No characters are connected yet. At least one character has to be connected to be able to post.',
  'sb_submit_text'                  => 'Send',
  'sb_save_wait'                    => 'Saving, please wait...',
  'sb_reload'                       => 'Reload',
  'sb_no_entries'                   => 'No entries',
  'sb_archive'                      => 'Archive',
  'sb_shoutbox_archive'             => 'Shoutbox Archive',

  // Exchange
  'sb_missing_char_id'              => 'Invalid Member ID entered',
  'sb_missing_text'                 => 'Missing text to insert',

  // About/Credits
  'sb_about_header'                 => 'About Shoutbox',
  'sb_credits_part1'                => 'Shoutbox v',
  'sb_credits_part2'                => ' by Aderyn',
  'sb_copyright'                    => 'Copyright',
);

?>
