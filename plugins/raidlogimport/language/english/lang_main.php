<?php
 /*
 * Project:     EQdkp-Plus Raidlogimport
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2013-01-23 20:27:42 +0100 (Wed, 23 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2008-2009 hoofy_leon
 * @link        http://eqdkp-plus.com
 * @package     raidlogimport
 * @version     $Rev: 12892 $
 *
 * $Id: lang_main.php 12892 2013-01-23 19:27:42Z hoofy_leon $
 */
	$lang['raidlogimport'] = 'Raid-Log-Import';
	$lang['action_raidlogimport_bz_upd'] = 'Boss / Zone edited';
	$lang['action_raidlogimport_bz_add'] = 'Boss / Zone added';
	$lang['action_raidlogimport_bz_del'] = 'Boss / Zone deleted';
	$lang['raidlogimport_long_desc'] = 'The plugin enables you to import most types of data in formatted text strings and create a raid from it. You can award points per boss and per hour.';
	$lang['raidlogimport_short_desc'] = 'Imports DKP-Strings';
    $lang['links'] = 'Links';

	//permissions
	$lang['raidlogimport_bz'] = 'Boss/Zone Management';
	$lang['raidlogimport_dkp'] = 'Import a raid-log';
	

	//Bz
	$lang['rli_bz_bz'] = 'Bosses / Zones';
	$lang['rli_bz_abz'] = 'Active Bosses / Zones';
	$lang['rli_bz_ibz'] = 'Inactive Bosses / Zones';
	$lang['bz_boss'] = 'Bosses';
	$lang['bz_boss_s'] = 'Boss';
	$lang['bz_boss_oz'] = 'Bosses without Zone';
	$lang['bz_zone'] = 'Zones';
	$lang['bz_zone_s'] = 'Zone';
	$lang['bz_no_zone'] = 'no Zone';
	$lang['bz_string'] = 'String';
	$lang['bz_bnote'] = 'Note';
	$lang['bz_bonus'] = 'Bonus-DKP / DKP/h';
	$lang['bz_timebonus'] = 'Punkte pro Stunde';
	$lang['bz_diff'] = 'Schwierigkeit';
	$lang['bz_zevent'] = 'Event';
	$lang['bz_update'] = 'Add new / Edit marked';
	$lang['bz_delete'] = 'Delete marked';
	$lang['bz_upd'] = 'Edit Bosses / Zones';
	$lang['bz_type'] = 'Type';
	$lang['bz_note_event'] = 'Note / Event';
	$lang['bz_save'] = 'Save';
	$lang['bz_yes'] = 'Yes!';
	$lang['bz_no'] = 'No!';
	$lang['bz_no_id'] = 'Nothing selected.';
	$lang['bz_del'] = 'Delete Bosses / Zones';
	$lang['bz_confirm_del'] = 'Do you really want to delete this?';
	$lang['bz_no_del'] = 'Data not deleted!';
	$lang['bz_del_suc'] = 'Data successfully deleted.';
	$lang['bz_tozone'] = 'In zone';
	$lang['bz_no_save'] = 'Data not savedt!';
	$lang['bz_save_suc'] = 'Data successfully saved.';
	$lang['bz_suc'] = 'Bosses / Zones Success';
	$lang['bz_missing_values'] = 'All fields have to be filled in.';
	$lang['bz_sort'] = 'Order';
	$lang['bz_copy_zone'] = 'Copy marked zone (including bosses) to difficulty: ';
	$lang['bz_copy_suc'] = 'Copy successful.';
	$lang['bz_no_copy'] = 'Copy failed!';
	$lang['bz_import_boss'] = 'Import boss';
	$lang['bz_set_inactive'] = 'Toggle active/inactive for marked zones (including bosses)';
	$lang['bz_active_suc'] = 'Active/inactive toggled for marked zones.';

	//dkp
	$lang['rli_dkp_insert'] = 'Insert DKP-String';
	$lang['rli_data_source'] = 'Select data-source';
	$lang['rli_continue_old'] = 'Continue previous import';
	$lang['rli_send'] = 'Send';
	$lang['rli_raidinfo'] = 'Raid Infos';
	$lang['rli_start'] = 'Start';
	$lang['rli_end'] = 'End';
	$lang['rli_bosskills'] = 'Bosskills';
	$lang['rli_cost'] = 'Cost';
	$lang['rli_success'] = 'Success';
	$lang['rli_error'] = 'Data not saved because of an error!';
	$lang['rli_no_mem_create'] = ' could not be created. Please add him manually!';
	$lang['rli_mem_auto'] = ' was automatically created.';
	$lang['rli_raid_to'] = 'Raid to %1$s on %2$s';
	$lang['rli_t_points'] = 'Time-DKP';
	$lang['rli_b_dkp'] = 'Boss-DKP';
	$lang['rli_looter'] = 'Looter';
	$lang['xml_error'] = 'XML-Error. Please check the log!';
	$lang['parse_error'] = 'Parsing-Error!';
	$lang['rli_check_data'] = 'Check data';
	$lang['rli_clock'] = '';
	$lang['rli_hour'] = 'hour';
	$lang['rli_att'] = 'Attendence';
	$lang['rli_checkmem'] = 'Check member-data';
	$lang['rli_back2raid'] = 'Back to raids';
	$lang['rli_checkraid'] = 'Check raids';
	$lang['rli_checkitem'] = 'Check Items';
	$lang['rli_itempage'] = 'Itempage ';
	$lang['rli_back2mem'] = 'Back to members';
	$lang['rli_back2item'] = 'Back to items';
    $lang['rli_checkadj'] = 'Check Adjustments';
    $lang['rli_calc_note_value'] = 'Recalculate raidvalue and raidnote';
	$lang['rli_insert'] = 'Insert DKP';
	$lang['rli_adjs'] = 'Adjustments';
	$lang['rli_partial_raid'] = 'Partial Raidattendence';
	$lang['rli_add_raid'] = 'Add raid';
	$lang['rli_add_raids'] = 'Add raids';
	$lang['rli_delete_raids_warning'] = 'Do you really want to delete the raid/boss?';
	$lang['rli_add_mem'] = 'Add member';
	$lang['rli_add_mems'] = 'Add members';
	$lang['rli_delete_members_warning'] = 'Do you really want to delete the member?';
	$lang['rli_add_time'] = 'Add a timebar';
	$lang['rli_del_time'] = 'Delete timebar';
	$lang['rli_standby_switch'] = 'Toggle standby';
	$lang['rli_bossname'] = 'Name of the boss:';
	$lang['rli_bosstime'] = 'Killtime:';
	$lang['rli_bossvalue'] = 'Value / Bonus:';
	$lang['rli_add_item'] = 'Add item';
	$lang['rli_delete_items_warning'] = 'Do you really want to delete the item?';
	$lang['rli_item_id'] = 'Item-ID';
	$lang['rli_add_adj'] = 'Add adjustment';
	$lang['rli_add_adjs'] = 'Add adjustments';
	$lang['rli_add_bk'] = 'Add bosskill';
	$lang['rli_add_bks'] = 'Add bosskills';
	$lang['rli_imp_no_suc'] = 'Import not successful';
	$lang['rli_imp_suc'] = 'Import successful';
	$lang['rli_members_needed'] = 'No members given.';
	$lang['rli_raids_needed'] = 'No raids given.';
	$lang['rli_missing_values'] = 'There are missing some values. Please: ';
	$lang['rli_miss'] = 'The following nodes are missing: ';
	$lang['rli_lgaobk'] = 'Log guild attendees on bosskill must be deactivated, before tracking. If you want to import the log anyway, you have to delete all the joins which have the same time as the bosskills.';
	$lang['wrong_format'] = 'The parser you haven chosen and the raid-log you have posted, do not match.';
	$lang['eqdkp_format'] = 'Please set the options of your CT-Raidtracker to <img src="'.registry::get_const('root_path').'plugins/raidlogimport/images/eqdkp_options.png">';
	$lang['plus_format'] = 'Please set the output of your Tracker to EQdkpPlus XML Format';
	$lang['magicdkp_format'] = 'An error occured.';
	$lang['wrong_game'] = 'The game from which you exported the log and the game you specified in the configuration are not the same!';
	$lang['rli_process'] = 'Process';
	$lang['check_raidval'] = 'Check raid values';
	$lang['rli_choose_mem'] = 'Choose a Member ...';
	$lang['rli_go_on'] = 'Forward';
	$lang['rli_raidatt_upd'] = 'Click on "Update" to show the raid attendance for the new times.';
	$lang['rli_error_imagecreate'] = 'Error while creating image file.';
	$lang['rli_save_itempool'] = 'Save itempool for marked items.';
	$lang['rli_itempool_saved'] = 'Itempools saved!';
	$lang['rli_itempool_partial_save'] = 'Itempools saved only partially.';
	$lang['rli_itempool_nosave'] = 'Not saved Items';
	$lang['rli_help'] = 'Help?';
	$lang['rli_help_dt_member'] = 'help text NYI';
	$lang['rli_member_refresh_for_view'] = 'Press update to show the Raidslider.';
	$lang['rli_loading'] = 'Please wait';
	$lang['rli_finish'] = 'Finish';

	// error messages
	$lang['rli_error_member_create'] = 'Creation of character %s failed.';
	$lang['rli_error_no_raid'] = 'At least one raid needs to be created.';
	$lang['rli_error_no_attendant']  = 'The must be at least one member participating the raid.';
	$lang['rli_error_no_buyer'] = 'Could not find the buyer of the Item %s in the raid or database.';
	$lang['rli_error_item_no_raid'] = 'Item %s have not been assigned to a raid.';
	
	//config
	$lang['new_member_rank'] = 'Default rank for automatic created members.';
	$lang['raidcount'] = 'How should the raids be created?';
	$lang['raidcount_0'] = 'One raid for everything';
	$lang['raidcount_1'] = 'One raid per hour';
	$lang['raidcount_2'] = 'One raid per boss';
	$lang['raidcount_3'] = 'One raid per hour and per boss';
	//moved from dkp
	$lang['wrong_settings'] = '<img src="$eqdkp_root_path'.'images/global/false.png" alt="error" width="32"> Wrong Settings!';
	$lang['wrong_settings_1'] = $lang['wrong_settings'].' You cannot combine '.$lang['raidcount_1'].' with no Time-DKP.';
	$lang['wrong_settings_2'] = $lang['wrong_settings'].' You cannot combine '.$lang['raidcount_2'].' with no Boss-DKP.';
	$lang['wrong_settings_3'] = $lang['wrong_settings'].' You cannot combine '.$lang['raidcount_3'].' with no Boss- and/or Time-DKP.';
	
	$lang['attendence_begin'] = 'Bonus for attendence during raidbegin';
	$lang['attendence_end'] = 'Bonus for attendence during raidend';
	$lang['config_success'] = 'Configuration Success';
	$lang['event_boss'] = 'Exists an event for each boss?';
	$lang['event_boss_1'] = 'Yes';
	$lang['event_boss_2'] = 'Use the name of the boss as raid-note';
	$lang['attendence_raid'] = 'Should an extra raid be created for attendency?';
	$lang['loottime'] = 'Time in seconds, the loot belongs to the boss before.';
	$lang['attendence_time'] = 'Time in seconds, the invite / end of raid lasts.';
	$lang['rli_inst_version'] = 'Installed version';
	$lang['bz_parse'] = 'Delimiter between the Strings, which belong to one "event".';
	$lang['parser'] = 'In which XML-Format is the string?';
	$lang['parser_eqdkp'] = 'MLDKP 1.1 / EQdkp Plugin';
	$lang['parser_plus'] = 'EQdkpPlus XML Format';
	$lang['parser_magicdkp'] = 'MagicDKP';
	$lang['parser_empty'] = 'Empty String';
	$lang['rli_man_db_up'] = 'Force DB-Update';
	$lang['rli_upd_check'] = 'Enable Update Check?';
	$lang['use_dkp'] = 'Which DKP shall be used?';
	$lang['use_dkp_1'] = 'Boss-DKP';
	$lang['use_dkp_2'] = 'Time-DKP';
	$lang['use_dkp_4'] = 'Event-DKP';
	$lang['null_sum'] = 'Use Null-Sum-System?';
	$lang['null_sum_0'] = 'No';
	$lang['null_sum_1'] = 'Every member in the raid gets the DKP';
	$lang['null_sum_2'] = 'Every member in the system gehts the DKP';
	$lang['deactivate_adj'] = "Deactivate Adjustments?";
	$lang['deactivate_adj_warn'] = "This removes partially gain of DKP per member! Everyone gets all or nothing!";
	$lang['auto_minus'] = 'Activate automatic minus?'.$lang['addinfo_am'];
	$lang['auto_minus_help'] = "When used, member, who did not join the last x raids, loose an amount of DKP. If you use zero-sum the member will be awarded an item, else he gets an adjustment.";
	$lang['am_raidnum'] = 'Number of raids for automatic minus';
	$lang['am_value'] = 'Amount of DKP drawn off';
	$lang['am_value_raids'] = 'DKP value = DKP of last number of raids';
	$lang['am_allxraids'] = "Reset raidcount on Minus-DKP?";
	$lang['am_allxraids_help'] = "Example: A member looses DKP after 3 Raids of not being there. The 4th Raid he is missing again. If this option is deactivated, the member will loose DKP again. If its activated he will loose the DKP on his 6th Raid of not being there.";
	$lang['am_name'] = 'lack of participation';
	$lang['title_am'] = 'Automatic Minus';
	$lang['title_adj'] = 'Adjustments';
	$lang['title_att'] = 'Attendence';
	$lang['title_general'] = 'General';
	$lang['title_loot'] = 'Loot / Items';
	$lang['title_parse'] = 'Parse Settings';
	$lang['title_hnh_suffix'] = 'Heroic / Non-Heroic';
	$lang['title_member'] = 'Member Settings';
	$lang['ignore_dissed'] = 'Ignore disenchanted and bank loot?';
	$lang['ignore_dissed_help'] = 'e.g. Disenchanted or bank. Separarated by commata.';
	$lang['member_miss_time'] = 'Time in seconds a member can miss without it being tracked.';
	$lang['s_member_rank'] = 'Show member rank?';
	$lang['s_member_rank_1'] = 'Members-Overview';
	$lang['s_member_rank_2'] = 'Loot-Overview';
	$lang['s_member_rank_4'] = 'Adjustments-Overview';
	$lang['member_start'] = 'Start-DKP a Member gains, when he is automatically created';
	$lang['member_start_name'] = 'Start-DKP'; //value is used for reason of adjustment
	$lang['member_start_event'] = 'Event for Start-DKP';
	$lang['member_raid'] = 'How many % of attendance do a member need to get the particiaption in the raid?';
	$lang['att_note_begin'] = 'raid note of the start-attendence-raid';
	$lang['att_note_end'] = 'raid note of the end-attendence-raid';
	$lang['raid_note_time']	= 'raid note of the raids per hour';
	$lang['raid_note_time_0'] = '20:00-21:00, 21:00-22:00, etc.';
	$lang['raid_note_time_1'] = '1.Hour, 2.Hour, etc.';
	$lang['timedkp_handle']	= "Calculation of Timedkp";
	$lang['timedkp_handle_help'] = "0: exact calculation per minute <br /> &gt;0: minutes, after the member gains full dkp of the hour";
	$lang['member_display'] = 'How should the member-list be displayed?';
	$lang['member_display_1'] = 'Multiple Checkboxes';
	$lang['member_display_0'] = 'Multi-Select';
	$lang['member_display_2'] = 'Detailed Join/Leave Information';
    $lang['member_display_add'] = "If you want to use '".$lang['member_display_1']."' or '".$lang['member_display_2']."' you must have the GD-lib (PHP-Extension). You are running the following GD-lib version: %s";
	$lang['no_gd_lib'] = '<span class="negative">no GD-lib found</span>';
    $lang['title_standby'] = 'Standby-Settings';
    $lang['standby_raid'] = 'Shall the standby-members be assigned to a raid?';
    $lang['standby_raid_0'] = 'No.';
    $lang['standby_raid_1'] = 'Yes, create an extra raid.';
    $lang['standby_raid_2'] = 'Yes, assign them to the normal raid(s).';
    $lang['standby_absolute'] = 'Shall the standby DKP be absolute?';
    $lang['standby_value'] = 'How much percent of the DKP or rather how many DKP absolute, shall the standby-members get?';
	$lang['standby_att'] = 'Shall standby-members gain start/end-DKP?';
	$lang['standby_att_1'] = 'Start-DKP';
	$lang['standby_att_2'] = 'End-DKP';
	$lang['standby_dkptype'] = 'Which DKP shall standby-members get?';
	$lang['standby_dkptype_1'] = $lang['use_dkp_1'];
	$lang['standby_dkptype_2'] = $lang['use_dkp_2'];
	$lang['standby_dkptype_4'] = $lang['use_dkp_4'];
	$lang['standby_raidnote'] = 'Note for standby-raid';
	$lang['standby_raid_note'] = 'Standby';
	$lang['itempool_save'] = 'Itempools can be saved per item and event.';
	$lang['itempool_save_help'] = 'At the item-import-page the itempool can be saved for all displayed items. On the next import of the raid the itempool is automatically selected for that item.';
	$lang['del_dbl_times'] = 'Shall double times be deleted? The latter time for joins, the earlier one for leaves.';
	$lang['autocomplete'] = 'Add autocomplete function to the following fields?';
	$lang['autocomplete_1'] = 'Charactername';
	$lang['autocomplete_2'] = 'Itemname';
	$lang['no_del_warn'] = 'Dont show warnings on deletion?';

    //portal
    $lang['p_rli_zone_display'] = 'Which zones shall be displayed?';
    $lang['dkpvals'] = 'DKP-Values';
?>