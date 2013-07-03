<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-11-11 19:07:23 +0100 (So, 11. Nov 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12435 $
 * 
 * $Id: mumbleviewer_portal.class.php 12435 2012-11-11 18:07:23Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class mumbleviewer_portal extends portal_generic {
	public static function __shortcuts() {
		$shortcuts = array('core', 'config', 'tpl', 'pdc');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	protected $path		= 'mumbleviewer';
	protected $data		= array(
		'name'			=> 'Mumble Viewer',
		'version'		=> '1.1.0',
		'author'		=> 'Mike Johnson',
		'contact'		=> 'support@commandchannel.com',
		'description'	=> 'Displays Mumble server information',
	);
	protected $positions = array('middle', 'left1', 'left2', 'right', 'bottom');
	protected $settings	= array(
			'pk_mumbleviewer_datauri'     => array(
			'name'      => 'pk_mumbleviewer_datauri',
			'language'  => 'pk_mumbleviewer_datauri',
			'property'  => 'text',
			'size'      => '30',
			'help'      => 'pk_mumbleviewer_datauri_help',
		),
		'pk_mumbleviewer_dataformat'     => array(
			'name'      => 'pk_mumbleviewer_dataformat',
			'language'  => 'pk_mumbleviewer_dataformat',
			'property'  => 'dropdown',
			'size'      => '30',
			'options'	=> array('json' => 'JSON', 'xml' => 'XML'),
			'help'      => 'pk_mumbleviewer_dataformat_help',
		),
		'pk_mumbleviewer_iconstyle'     => array(
			'name'      => 'pk_mumbleviewer_iconstyle',
			'language'  => 'pk_mumbleviewer_iconstyle',
			'property'  => 'dropdown',
			'options'	=> array('mumbleViewerIconsDefault' => 'Default', 'mumbleViewerIconsFarCry2' => 'Far Cry 2', 'mumbleViewerIconsNextGen' => 'NextGen', 'mumbleViewerIconsSCGermania' => 'SC Germania'),
			'help'      => 'pk_mumbleviewer_iconstyle_help',
		)
	);
	protected $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '7',
	);
	
	public function output() {
		$dataUri = $this->config->get('pk_mumbleviewer_datauri');
		$dataFormat = $this->config->get('pk_mumbleviewer_dataformat');
		$iconStyle = $this->config->get('pk_mumbleviewer_iconstyle');
		$cachetime = 30; //cachetime = 30 seconds
		
		$this->tpl->css_file($this->root_path . 'portal/mumbleviewer/mumbleChannelViewer.css');
		
		$output = $this->pdc->get('portal.modul.mumbleviewer.outputdata', false, true);
		if ((!$output) or $cachetime == 0){
		
			$output = "<div id='mumbleViewer' class='".$iconStyle."'>";

			if ( $dataUri && $dataFormat ) {
				$mumbleViewerInclude = $this->root_path . 'portal/mumbleviewer/mumbleChannelViewer.php';
				if (is_file($mumbleViewerInclude)) {
					require_once( $mumbleViewerInclude );
					$output .= MumbleChannelViewer::render( html_entity_decode( $dataUri ), $dataFormat );
				}
			}
			$output .= "</div>";
			if ($cachetime >= 1) {$this->pdc->put('portal.modul.mumbleviewer.outputdata', $output, $cachetime, false, true);}
		}

		return $output;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_mumbleviewer_portal', mumbleviewer_portal::__shortcuts());
?>