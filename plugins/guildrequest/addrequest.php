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

class guildrequestAddrequest extends page_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('pm', 'user', 'core', 'in', 'pdh', 'time', 'tpl', 'html', 'email' => 'MyMailer');
    return array_merge(parent::$shortcuts, $shortcuts);
  }
  
  private $data = array();

  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('guildrequest', PLUGIN_INSTALLED))
      message_die($this->user->lang('gr_plugin_not_installed'));

    $handler = array(
      'save' => array('process' => 'save', 'csrf' => true, 'check' => 'u_guildrequest_add'),
    );
    parent::__construct('u_guildrequest_add', $handler);

    $this->process();
  }

  public function save(){
	//Build Field-Array
	$arrFields = $this->pdh->get('guildrequest_fields', 'id_list', array());
	$arrInput = array();
	foreach($arrFields as $id){
		$row = $this->pdh->get('guildrequest_fields', 'id', array($id));
		if ($row['type'] == 3 || $row['type'] == 4){
			continue;
		}
		$arrInput[$row['name']] = array(
			'id'		=> $row['id'],
			'input' 	=> $this->in->get('gr_field_'.$row['id']),
			'required'	=> ($row['required']),
		);
		if ($row['type'] == 5){
			$arrInput[$row['name']] = array(
				'id'		=> $row['id'],
				'input' 	=> serialize($this->in->getArray('gr_field_'.$row['id'], 'int')),
				'required'	=> ($row['required']),
			);
		}
	}
	$arrInput[$this->user->lang('email')] = array(
		'input' 	=> $this->in->get('gr_email'),
		'required'	=> true,
	);
	$arrInput[$this->user->lang('name')] = array(
		'input' 	=> $this->in->get('gr_name'),
		'required'	=> true,
	);

	$this->data = $arrInput;

	//Check Captcha	
	require($this->root_path.'libraries/recaptcha/recaptcha.class.php');
	$captcha = new recaptcha;
	$response = $captcha->recaptcha_check_answer ($this->config->get('lib_recaptcha_pkey'), $this->env->ip, $this->in->get('recaptcha_challenge_field'), $this->in->get('recaptcha_response_field'));
	if (!$response->is_valid) {
		$this->core->message($this->user->lang('lib_captcha_wrong'), $this->user->lang('error'), 'red');
		$this->display;
		return;
	}
	
	//Check email
	if (!preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/",$this->in->get('gr_email'))){
		$this->core->message($this->user->lang('fv_invalid_email'), $this->user->lang('error'), 'red');
		$this->display();
		return;
	}
	
	//Check Required
	$arrRequired = array();
	foreach ($arrInput as $key => $val){
		if (!$val['required']) continue;
		if ($val['input'] == '' || $val['input'] == 'a:0:{}') $arrRequired[] = $key;
	}
	if (count($arrRequired) > 0) {
		$this->core->message(implode(', ', $arrRequired), $this->user->lang('missing_values'), 'red');
		$this->display();
		return;
	}

	//Insert into DB
	
	$strName = $arrInput[$this->user->lang('name')]['input'];
	$strEmail = $arrInput[$this->user->lang('email')]['input'];
	$strAuthKey = random_string(false, 40);
	$strActivationKey = random_string(false, 32);
	$arrInput[$this->user->lang('email')]['input'] = register('encrypt')->encrypt($arrInput[$this->user->lang('email')]['input']);
	$arrToSave = array();
	foreach($arrInput as $val){
		$arrToSave[$val['id']] = $val['input']; 
	}	
	$strContent = serialize($arrToSave);
	
	$blnResult = $this->pdh->put('guildrequest_requests', 'add', array($strName, $strEmail, $strAuthKey, $strActivationKey, $strContent));
	
	$this->pdh->process_hook_queue();
	if (!$blnResult){
		$this->core->message($this->user->lang('error'), $this->user->lang('error'), 'red');
		$this->display();
		return;
	}
	
	//Send Email to User with activation Key
	/*
	$server_url = $this->env->link.'plugins/guildrequest/activate.php';
	$bodyvars = array(
		'USERNAME'		=> $strName,
		'U_ACTIVATE' 	=> $server_url . '?key=' . $strActivationKey,
		'GUILDTAG'		=> $this->config->get('guildtag'),
	);
	
	if(!$this->email->SendMailFromAdmin($strEmail, $this->user->lang('gr_activationmail_subject'), $this->root_path.'plugins/guildrequest/language/'.$this->user->data['user_lang'].'/email/request_activation.html', $bodyvars)){
		$this->core->message($this->user->lang('email_subject_send_error'), $this->user->lang('error'), 'red');
		$this->display();
		return;
	}*/
	
	//Send Email to User with auth key
	$server_url = $this->env->link.'plugins/guildrequest/viewrequest.php';
	$bodyvars = array(
		'USERNAME'		=> sanitize($strName),
		'U_ACTIVATE' 	=> $server_url . '?id='.$blnResult.'&key=' . $strAuthKey,
		'GUILDTAG'		=> $this->config->get('guildtag'),
	);
	
	if(!$this->email->SendMailFromAdmin($strEmail, $this->user->lang('gr_viewlink_subject'), $this->root_path.'plugins/guildrequest/language/'.$this->user->data['user_lang'].'/email/request_viewlink.html', $bodyvars)){
		$this->core->message($this->user->lang('email_subject_send_error'), $this->user->lang('error'), 'red');
		$this->display();
		return;
	} else {
		//Send Notification Mail to everyone who wants it
		$bodyvars = array(
			'U_VIEW' 		=> $server_url . '?id='.$blnResult,
			'REQUEST_USER'	=> sanitize($strName),
			'GUILDTAG'		=> $this->config->get('guildtag'),
		);
		
		$arrUserIDs = $this->pdh->get('user', 'id_list', array());
		foreach($arrUserIDs as $userid){
			$arrGuildrequestSettings = $this->pdh->get('user', 'plugin_settings', array($userid, 'guildrequest'));
			if (isset($arrGuildrequestSettings['gr_send_notification_mails']) && $arrGuildrequestSettings['gr_send_notification_mails']){
				$strEmail = $this->pdh->get('user', 'email', array($userid, true));
				if ($strEmail != ''){
					$bodyvars['USERNAME'] = $this->pdh->get('user', 'name', array($userid));
					$this->email->SendMailFromAdmin($strEmail, $this->user->lang('gr_notification_subject'), $this->root_path.'plugins/guildrequest/language/'.$this->user->data['user_lang'].'/email/request_notification.html', $bodyvars);
				}
			}
		}
	
	
		//Redirect to viewrequest page
		redirect('plugins/guildrequest/viewrequest.php?id='.$blnResult.'&key=' . $strAuthKey.'&msg=success');
	}
  }
  
  
  public function display()
  {
	
	$arrFields = $this->pdh->get('guildrequest_fields', 'id_list', array());
	$intGroup = 0;
	$blnGroupOpen = false;
	$blnPersonalGroup = false;
	$this->tpl->assign_block_vars('tabs', array(
	));
	
	$this->add_personal_group();
	
	
	foreach($arrFields as $id){
		$row = $this->pdh->get('guildrequest_fields', 'id', array($id));
		$row['options'] = unserialize($row['options']);
		
		//Close previous group
		if ($row['type'] == 3 || $row['type'] == 4){
			$blnGroupOpen = false;
			$intGroup++;
		}
		
		//Input
		if ($row['type'] == 0){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
			
			$options = array(
				'fieldtype' => 'text',
				'name'		=> 'gr_field_'.$row['id'],
				'javascript'=> 'style="width:95%"',
				'value'		=> isset($this->data[$row['name']]) ? $this->data[$row['name']]['input'] : '',
			);
			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
				'NAME'		=> $row['name'],
				'FIELD'		=> $this->html->widget($options),
				'REQUIRED'	=> ($row['required']),
				'HELP'		=> $row['help'],
			));
			
		}
		
		//Textarea
		if ($row['type'] == 1){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}

			
			$options = array(
				'fieldtype' => 'textarea',
				'name'		=> 'gr_field_'.$row['id'],
				'javascript'=> 'style="width:95%"',
				'rows'		=> 10,
				'value'		=> isset($this->data[$row['name']]) ? $this->data[$row['name']]['input'] : '',
			);
			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
				'NAME'		=> $row['name'],
				'FIELD'		=> $this->html->widget($options),
				'REQUIRED'	=> ($row['required']),
				'HELP'		=> $row['help'],
			));
		}
		
		//Select
		if ($row['type'] == 2){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
			
			$arrOptions = array();
			$arrOptions[''] = $this->user->lang('cl_ms_noneselected');
			foreach($row['options'] as $val){
				$arrOptions[$val] = $val;
			}
			
			$options = array(
				'fieldtype' => 'dropdown',
				'name'		=> 'gr_field_'.$row['id'],
				'options'	=> $arrOptions,
				'no_lang'	=> true,
				'selected'	=> isset($this->data[$row['name']]) ? $this->data[$row['name']]['input'] : '',
			);
			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
				'NAME'		=> $row['name'],
				'FIELD'		=> $this->html->widget($options),
				'REQUIRED'	=> ($row['required']),
				'HELP'		=> $row['help'],
			));
		}
		
		//Group Label
		if ($row['type'] == 3){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $row['name'],
					'ID'	=> utf8_strtolower(str_replace(' ', '', $row['name'])),
				));
				$blnGroupOpen = true;
			}
		}
		
		//Plain text
		if ($row['type'] == 4){
			$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'			=> $row['name'],
					'S_NO_FIELDSET' => true,
			));
		}
		
		//Checkboxes
		if ($row['type'] == 5){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
			
			$field = '';
			
			$selected = isset($this->data[$row['name']]) ? unserialize($this->data[$row['name']]['input']) : array();
			
			foreach($row['options'] as $val){
				$options = array(
					'fieldtype' => 'checkbox',
					'name'		=> 'gr_field_'.$row['id'].'['.trim($val).']',
					'options'	=> trim($val),
					'no_lang'	=> true,
					'selected'	=> isset($selected[trim($val)]) ? $selected[trim($val)] : '',
					'text'		=> trim($val),
				);
				$field .= $this->html->widget($options).'&nbsp;&nbsp;&nbsp;';
			}
			

			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
				'NAME'		=> $row['name'],
				'FIELD'		=> $field,
				'REQUIRED'	=> ($row['required']),
				'HELP'		=> $row['help'],
			));
		}
		
		//Radioboxes
		if ($row['type'] == 6){
			if (!$blnGroupOpen){
				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'	=> $this->user->lang('gr_default_grouplabel'),
					'ID'	=> 'information',
				));
				$blnGroupOpen = true;
			}
			
			$arrOptions = array();
			foreach($row['options'] as $val){
				$arrOptions[trim($val)] = trim($val);
			}
			
			$options = array(
				'fieldtype' => 'radio',
				'name'		=> 'gr_field_'.$row['id'],
				'options'	=> $arrOptions,
				'no_lang'	=> true,
				'selected'	=> isset($this->data[$row['name']]) ? $this->data[$row['name']]['input'] : '',
			);
			$this->tpl->assign_block_vars('tabs.fieldset.field', array(
				'NAME'		=> $row['name'],
				'FIELD'		=> $this->html->widget($options),
				'REQUIRED'	=> ($row['required']),
				'HELP'		=> $row['help'],
			));
		}
	}
	
	require($this->root_path.'libraries/recaptcha/recaptcha.class.php');
	$captcha = new recaptcha;
	$this->tpl->assign_vars(array(
		'CAPTCHA'				=> $captcha->recaptcha_get_html($this->config->get('lib_recaptcha_okey')),
		'S_DISPLAY_CATPCHA'		=> true,
	));
	
    // -- EQDKP ---------------------------------------------------------------
    $this->core->set_vars(array (
      'page_title'    => $this->user->lang('gr_add'),
      'template_path' => $this->pm->get_data('guildrequest', 'template_path'),
      'template_file' => 'addrequest.html',
      'display'       => true
    ));
	
	
  }
  
  private function add_personal_group(){
	$this->tpl->assign_block_vars('tabs.fieldset', array(
		'NAME'	=> $this->user->lang('gr_personal_information'),
		'ID'	=> 'personal_information',
	));
	$options = array(
		'fieldtype' => 'text',
		'name'		=> 'gr_name',
		'javascript'=> 'style="width:95%"',
		'value'		=> isset($this->data[$this->user->lang('name')]) ? $this->data[$this->user->lang('name')]['input'] : '',
	);
	$this->tpl->assign_block_vars('tabs.fieldset.field', array(
		'NAME'		=> $this->user->lang('name'),
		'FIELD'		=> $this->html->widget($options),
		'REQUIRED'	=> true,
	));
	
	$options = array(
		'fieldtype' => 'text',
		'name'		=> 'gr_email',
		'javascript'=> 'style="width:95%"',
		'value'		=> isset($this->data[$this->user->lang('email')]) ? $this->data[$this->user->lang('email')]['input'] : '',
	);
	$this->tpl->assign_block_vars('tabs.fieldset.field', array(
		'NAME'		=> $this->user->lang('email'),
		'FIELD'		=> $this->html->widget($options),
		'REQUIRED'	=> true,
		'HELP'		=> $this->user->lang('gr_email_help'),
	));
  }
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_guildrequestAddrequest', guildrequestAddrequest::__shortcuts());
register('guildrequestAddrequest');

?>