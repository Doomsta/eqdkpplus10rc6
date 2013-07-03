<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2013-01-05 09:33:27 +0100 (Sat, 05 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12742 $
 * 
 * $Id: zip.class.php 12742 2013-01-05 08:33:27Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
if (!class_exists("zip")) {
	class zip extends gen_class {
		public static $shortcuts = array('pfh');

		private $zipfile = false;
		private $objZip = false;
		private $files = array();

		public function __construct($zipfile){
			$this->zipfile = $zipfile;
			if (class_exists("ZipArchive")){
				$this->objZip = new ZipArchive;
				if (file_exists($this->zipfile)){
					$blnOpen = $this->objZip->open($this->zipfile);
					if (!$blnOpen){
						$this->objZip = false;
					}
				}
			} else {
				$this->objZip = false;
			}
		}
		
		public function __destruct(){
			if ($this->objZip){
				@$this->objZip->close();
			}
		}
		
		//Add files that will be written to the archiv when calling create()
		public function add($mixFiles, $strRemovePath = false, $strAddPath = false){

			if (is_array($mixFiles)){
				foreach ($mixFiles as $file){
					$localfilename = $file;
					if ($strRemovePath && strlen($strRemovePath) && strpos($file, $strRemovePath) === 0){
						$localfilename = substr($file, strlen($strRemovePath));
					}
					if ($strAddPath && strlen($strAddPath)){
						$localfilename = $strAddPath.$localfilename;
					}
					$this->files['add'][$localfilename] = $file;
					if (isset($this->files['delete'][$localfilename])) unset($this->files['delete'][$localfilename]);
				}

			} elseif (strlen($mixFiles)) {	
				if (is_dir($mixFiles)){
					$mixFiles = (substr($mixFiles, -1) != '/') ? $mixFiles.'/' : $mixFiles;
					
					$d = dir($mixFiles);

					while (FALSE !== ($entry = $d->read())){
						if ($entry == '.' || $entry == '..'){
							continue;
						}

						$Entry = $mixFiles . $entry;
						if (is_dir( $Entry )){
							$this->add($Entry, $strRemovePath, $strAddPath);
							continue;
						}
						
						$localfilename = $Entry;
						if ($strRemovePath && strlen($strRemovePath) && strpos($Entry, $strRemovePath) === 0){
							$localfilename = substr($Entry, strlen($strRemovePath));
						}
						if ($strAddPath && strlen($strAddPath)){
							$localfilename = $strAddPath.$localfilename;
						}
						$this->files['add'][$localfilename] = $Entry;
						if (isset($this->files['delete'][$localfilename])) unset($this->files['delete'][$localfilename]);
					}
					
				} else {
			
					$localfilename = $mixFiles;
					if ($strRemovePath && strlen($strRemovePath) && strpos($mixFiles, $strRemovePath) === 0){
						$localfilename = substr($mixFiles, strlen($strRemovePath));
					}
					if ($strAddPath && strlen($strAddPath)){
						$localfilename = $strAddPath.$localfilename;
					}
					$this->files['add'][$localfilename] = $mixFiles;
					if (isset($this->files['delete'][$localfilename])) unset($this->files['delete'][$localfilename]);
				}
			} else {
				return false;
			}
			return true;
		}
		
		//Delete Files from Filelist, nur for deleting files from an existing archive!
		public function delete($strPath){
			//Directory
			if (substr($strPath, -1) == '/'){
				foreach($this->files as $key => $value){
					if (strpos($key, $strPath) === 0){
						unset($this->files['add'][$key]);
						$this->files['delete'][$key] = $key;
					}
				}
				
			} else {
				//File
				if (isset($this->files['add'][$strPath])){
					unset($this->files['add'][$strPath]);
					$this->files['delete'][$strPath] = $strPath;
					return true;
				}
				return false;
			}
			
			
			return false;
		}
		
		//Call create, when you have finished adding and deleting files. Archive will be created in tmp-Folder and moved to the right folder
		public function create(){
			//existing archive
			if ($this->objZip && $this->objZip->numFiles > 0){
				$tmpExisting = $this->pfh->FilePath(md5(uniqid().rand()).'.zip', 'tmp');
				//Move archive to temp folder
				$this->pfh->copy($this->zipfile, $tmpExisting);
				
				
				//open existing zip
				$objZip = new ZipArchive;
				$resZip = $objZip->open($tmpExisting);
				if ($resZip){
					if (is_array($this->files['add'])){
						foreach ($this->files['add'] as $key => $value){
							$blnResult = $objZip->addFile($value, $key);
							if (!$blnResult) return false;
						}
					}
					if (is_array($this->files['delete'])){
						foreach ($this->files['delete'] as $key => $value){
							$blnResult = $objZip->deleteName($value, $key);
							//if (!$blnResult) return false;
						}
					}
					
					$this->objZip->close();
					$objZip->close();
					$this->pfh->FileMove($tmpExisting, $this->zipfile);
					return true;
				} else {
					return false;
				}
				
			} else {
				$strTempArchiv = $this->pfh->FilePath(md5(uniqid().rand()).'.zip', 'tmp');
				//Create new archive
				$blnOpen = $this->objZip->open($strTempArchiv, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
				if ($blnOpen){
					foreach ($this->files['add'] as $key => $value){
						$blnResult = $this->objZip->addFile($value, $key);
						if (!$blnResult) return false;
					}
					$this->objZip->close();
					$this->pfh->FileMove($strTempArchiv, $this->zipfile);
					return true;
				} else {
					$this->objZip = false;
					return false;
				}
			}
		}
		
		public function extract($strTargetFolder, $arrFiles = false){
			$strTargetFolder = (substr( $strTargetFolder, -1 ) != '/') ? $strTargetFolder.'/' : $strTargetFolder;
		
			if ($this->objZip){
				for ( $i=0; $i < $this->objZip->numFiles; $i++ ) {
					$entry = $this->objZip->getNameIndex($i);
					
					if ($arrFiles && is_array($arrFiles)){
						if (!in_array($entry, $arrFiles)) continue;
					}

						//Directory
						if ( substr( $entry, -1 ) == '/' ) {
							$this->pfh->CheckCreateFolder($strTargetFolder.$entry);
						} else {
							//File
							$contents = '';
							$fp = $this->objZip->getStream($entry);
							if(!$fp) return false;

							while (!feof($fp)) {
								$contents .= fread($fp, 2);
							}

							fclose($fp);
							$this->pfh->CheckCreateFolder(pathinfo($strTargetFolder.$entry, PATHINFO_DIRNAME));
							$this->pfh->CheckCreateFile($strTargetFolder.$entry);
							$this->pfh->putContent($strTargetFolder.$entry, $contents);
						}
						
				}
				return true;
			}
			return false;
		}
	
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_zip', zip::$shortcuts);
?>