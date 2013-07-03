<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-08-20 15:13:47 +0200 (Mon, 20 Aug 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11943 $
 * 
 * $Id: encrypt.class.php 11943 2012-08-20 13:13:47Z godmod $
 */
 
if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found'); exit;
}

class encrypt extends gen_class {
	public static $shortcuts = array('config', 'user', 'core');
	
	private $resMycrypt;
	private $strKeyLength;
	private $strEncryptionKey;
		
	public function __construct($strEncryptionKey = ''){
		include_once($this->root_path.'libraries/aes/AES.class.php');
		
		if ($strEncryptionKey == '' && $this->encryptionKey == ''){
			$this->core->message('Encryption Key is missing. Please take a look at our Wiki.', $this->user->lang('error'), 'red');
			$this->strEncryptionKey = '';
		} else {		
			$this->strEncryptionKey = ($strEncryptionKey != '') ? $strEncryptionKey : $this->encryptionKey;
			$this->strEncryptionKey = md5($this->strEncryptionKey);		
		}
	}
	
	public function encrypt($strValue){
		if ($strValue == '' || $this->strEncryptionKey == '') return '';
		
		$strEncrypted = AesCtr::encrypt($strValue, $this->strEncryptionKey, 256);
		return $strEncrypted;

	}
	
	public function decrypt($strValue){
		if ($strValue == '' || $this->strEncryptionKey == '') return '';
		
		$strDecrypted = AesCtr::decrypt($strValue, $this->strEncryptionKey, 256);
		return $strDecrypted;

	}

} //END mmocms_encrypt-class

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_encrypt', encrypt::$shortcuts);
?>