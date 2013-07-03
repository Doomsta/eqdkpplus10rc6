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
 * $Id: archive.php 12273 2012-10-13 20:48:23Z godmod $
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('PLUGIN', 'guildrequest');

$eqdkp_root_path = './../../';
include_once($eqdkp_root_path.'common.php');

class guildrequestListrequests extends page_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('pm', 'user', 'core', 'in', 'pdh', 'time', 'tpl', 'html', 'email' => 'MyMailer', 'comments');
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
	
	$this->user->check_auth('u_guildrequest_view');
	
    $handler = array(
		//'vote' => array('process' => 'vote', 'csrf' => true, 'check' => 'u_guildrequest_vote'),
		'mark_all_read' => array('process' => 'mark_all_read', 'csrf' => 'true'),
    );
    parent::__construct(false, $handler,array('guildrequest_requests', 'username'), null, 'gr[]');

    $this->process();
  }
	
	public function mark_all_read(){
		$arrApplicationIDs = $this->pdh->get('guildrequest_requests', 'id_list', array());
		foreach($arrApplicationIDs as $intID){
			$this->pdh->put('guildrequest_visits', 'add', array($intID));
		}
		$this->pdh->process_hook_queue();
	}

	
  public function delete(){
	$this->user->check_auth('a_guildrequest_manage');
	$arrItems = $this->in->getArray('gr', 'int');
	foreach($arrItems as $id){
		$this->pdh->put('guildrequest_requests', 'delete', array($id));
	}
	$this->core->message($this->user->lang('gr_delete_success'), $this->user->lang('success'), 'green');
	
	$this->pdh->process_hook_queue();
  }
  
  public function display()
  {
	//Output
	$hptt_page_settings	= array (
  'name' => 'hptt_guildrequest',
  'table_main_sub' => '%request_id%',
  'table_subs' => array('%request_id%', '%field_id%'),
  'page_ref' => 'listrequests.php',
  'show_numbers' => false,
  'show_select_boxes' => false,
  'show_detail_twink' => false,
  'table_sort_col' => 0,
  'table_sort_dir' => 'asc',
  'table_presets' => 
  array (
    0 => 
    array (
      'name' => 'gr_checkbox',
      'sort' => true,
      'th_add' => 'width="20"',
      'td_add' => '',
    ),
    1 => 
    array (
      'name' => 'gr_date',
      'sort' => true,
      'th_add' => '',
      'td_add' => 'nowrap="nowrap"',
    ),
    2 => 
    array (
      'name' => 'gr_name',
      'sort' => true,
      'th_add' => 'width="50%"',
      'td_add' => 'nowrap="nowrap"',
    ),
    3 => 
    array (
      'name' => 'gr_email',
      'sort' => true,
      'th_add' => '',
      'td_add' => '',
    ),
  ),
);
	//Add colums
	$arrFields = $this->pdh->get('guildrequest_fields', 'id_list', array());
	foreach ($arrFields as $id){
		if ($this->pdh->get('guildrequest_fields', 'in_list', array($id)) && $this->pdh->get('guildrequest_fields', 'type', array($id)) < 3){
			$hptt_page_settings['table_presets'][] = array(
				 'name' 	=> 'gr_field_'.$id,
				  'sort' 	=> true,
				  'th_add'	=> '',
				  'td_add'	=> '',
			);
		}
	}
	
	$hptt_page_settings['table_presets'][] = array(
		 'name' => 'gr_status',
      'sort' => true,
      'th_add' => '',
      'td_add' => '',
	);
	$hptt_page_settings['table_presets'][] = array(
	'name' => 'gr_closed',
      'sort' => true,
      'th_add' => '',
      'td_add' => 'align="center"',
	);
	
	$hptt_page_settings['table_presets'][] = array(
		 'name' => 'gr_voting_flag',
      'sort' => true,
      'th_add' => 'width="20"',
      'td_add' => 'align="center"',
	);

		//Sort
		$sort			= $this->in->get('sort');
		$sort_suffix	= '&amp;sort='.$sort;

		$start				= $this->in->get('start', 0);
		$pagination_suffix	= ($start) ? '&amp;start='.$start : '';

	$view_list = $this->pdh->get('guildrequest_requests', 'id_list', array());
	$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'viewraid.php', '%link_url_suffix%' => ''), $this->user->id);

	//footer
	$raid_count			= count($view_list);
	$footer_text		= sprintf($this->user->lang('gr_footer'), $raid_count ,$this->user->data['user_rlimit']);

	$this->tpl->assign_vars(array (
		'PAGE_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix.$date_suffix, $start, $this->user->data['user_rlimit'], $footer_text),
		'GR_PAGINATION'		=> generate_pagination('listraids.php'.$this->SID.$sort_suffix.$date_suffix, $raid_count, $this->user->data['user_rlimit'], $start),
		'S_GR_ADMIN'		=> $this->user->check_auth('a_guildrequest_manage', false),
	));
	
	$this->confirm_delete($this->user->lang('gr_confirm_delete_requests'));
	
	$this->core->set_vars(array (
      'page_title'    => $this->user->lang('gr_view'),
      'template_path' => $this->pm->get_data('guildrequest', 'template_path'),
      'template_file' => 'listrequests.html',
      'display'       => true
    ));
  }
  

}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_guildrequestListrequests', guildrequestListrequests::__shortcuts());
register('guildrequestListrequests');

?>
