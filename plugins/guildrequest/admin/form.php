<?php
/*
 * Project:     EQdkp guildrequest
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-10-13 22:48:23 +0200 (Sa, 13. Okt 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     guildrequest
 * @version     $Rev: 12273 $
 *
 * $Id: settings.php 12273 2012-10-13 20:48:23Z godmod $
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'guildrequest');

$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path.'common.php');


/*+----------------------------------------------------------------------------
  | guildrequestForm
  +--------------------------------------------------------------------------*/
class guildrequestForm extends page_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('pm', 'user', 'config', 'core', 'in', 'jquery', 'html', 'tpl');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('guildrequest', PLUGIN_INSTALLED))
      message_die($this->user->lang('gr_plugin_not_installed'));

    $handler = array(
      'save' => array('process' => 'save', 'csrf' => true, 'check' => 'a_guildrequest_manage'),
    );
    parent::__construct('a_guildrequest_form', $handler, array('guildrequest_fields', 'name'), null, 'field_ids[]');

    $this->process();
  }

  /**
   * save
   * Save the configuration
   */
  public function save()
  {
	if (count($this->in->getArray('field', 'string')) > 0){
		//Truncate field table
		$this->pdh->put('guildrequest_fields', 'truncate', array());
		
		$id = 0;
		foreach($this->in->getArray('field', 'string') as $val){
			if ($val['name'] == '') continue;
			
			$strType = $val['type'];
			$strName = $val['name'];
			$strHelp = $val['help'];
			if (isset($val['options']) && $val['options'] != '') {
				$arrOptions = explode("\n", $val['options']);
			} else {
				$arrOptions = array();
			}
			$intSortID = $id;
			$intRequired = (isset($val['required']) && (int)$val['required']) ? 1 : 0;
			$intInList = (isset($val['in_list']) && (int)$val['in_list']) ? 1 : 0;
			
			$this->pdh->put('guildrequest_fields', 'add', array($val['id'], $strType, $strName, $strHelp, $arrOptions, $intSortID, $intRequired, $intInList));
			$id++;
		}
	}
	$this->pdh->process_hook_queue();
	
    // Success message
	$this->core->message($this->user->lang('sb_config_saved'), $this->user->lang('success'), 'green');
    $this->display($messages);
  }

  /**
   * display
   * Display the page
   *
   * @param    array  $messages   Array of Messages to output
   */
  public function display()
  {

	$this->tpl->add_js("
		$(\"#gr_form_table tbody\").sortable({
			cancel: '.not-sortable, input, .input',
			cursor: 'pointer',
		});
	", "docready");
	
	$this->confirm_delete($this->user->lang('gr_confirm_delete_field'));
	$this->jquery->selectall_checkbox('selall_fields', 'field_ids[]');
	
	$arrFields = $this->pdh->get('guildrequest_fields', 'id_list', array());
	foreach($arrFields as $id){
		$row = $this->pdh->get('guildrequest_fields', 'id', array($id));
		$row['options'] = unserialize($row['options']);
		$this->tpl->assign_block_vars('field_row', array(
			'KEY'				=> $row['id'],
			'NAME'				=> $row['name'],
			'HELP'				=> $row['help'],
			'TYP_DD'			=> $this->html->DropDown('field['.$row['id'].'][type]', $this->user->lang('gr_types'), $row['type'], '', 'onchange="type_change_listener(this)"'),
			'OPTIONS_DISABLED'	=> ($row['type'] != 2 && $row['type'] != 5 && $row['type'] != 6) ? 'disabled="disabled"' : '',
			'HELP_DISABLED'		=> ($row['type'] == 3 || $row['type'] == 4) ? 'disabled="disabled"' : '',
			'OPTIONS_HEIGHT'	=> ($row['type'] != 2 && $row['type'] != 5 && $row['type'] != 6) ? '20' : '60',
			'OPTIONS'			=> (count($row['options'])) ? implode("\n", $row['options']) : '',
			'REQUIRED'			=> ($row['required']) ? 'checked="checked"' : '',
			'IN_LIST'			=> ($row['in_list']) ? 'checked="checked"' : '',
		));
	}
		
	$this->tpl->assign_vars(array(
		'KEY'		=> max($arrFields)+1,
		'TYP_DD'	=> $this->html->DropDown('field[KEY][type]', $this->user->lang('gr_types'), '', '', 'onchange="type_change_listener(this)"'),
	));
		
    // -- EQDKP ---------------------------------------------------------------
    $this->core->set_vars(array(
      'page_title'    => $this->user->lang('guildrequest').' '.$this->user->lang('gr_manage_form'),
      'template_path' => $this->pm->get_data('guildrequest', 'template_path'),
      'template_file' => 'admin/form.html',
      'display'       => true
    ));
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_guildrequestForm', guildrequestForm::__shortcuts());
registry::register('guildrequestForm');

?>
