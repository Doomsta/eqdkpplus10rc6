<?php 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../../../';
include_once($eqdkp_root_path . 'common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#pages_dlg.title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js?v={tinymce_version}"></script>
	<script type="text/javascript" src="js/dialog.js?v={tinymce_version}"></script>
</head>
<body>
<?php if (register('user')->check_auth('a_pages_man', false)) { ?>
<form onsubmit="PagesDialog.insert();return false;" action="#">
	<p>{#pages_dlg.general}</p>
  <p>{#pages_dlg.select_page}: <?php echo register('html')->Dropdown('page', register('plus_datahandler')->get('pages', 'tiny_dropdown'), array()); ?></p>
  <p>{#pages_dlg.name}:
    <input id="urlname" name="urlname" type="text" class="text" /></p>
	<div class="mceActionPanel">
	  <div style="float: left">
			<input type="button" id="insert" name="insert" value="{#insert}" onclick="PagesDialog.insert();" />
		</div>

		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
		</div>
	</div>
</form>
<?php } else { ?>
Access denied.
<?php } ?>
</body>
</html>
