<?php 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../../../';
include_once($eqdkp_root_path . 'common.php');

$user = register('user');
$in = register('input');
$uploader = register('uploader');
$pfh = register('file_handler');
$html = register('html');

if ($user->check_auth('a_news_add', false) || $user->check_auth('a_news_upd', false) || $user->check_auth('a_pages_man', false)){

switch($in->get('action')){
	case 'delete' : $uploader->delete();
	break;
	case 'move' : $uploader->move();
}

if ($in->get('upload') != ""){
	$uploader->upload('upload', $in->get('folder', ''));
}

if ($in->get('create_folder') != ""){
	$uploader->create_folder();
}

	$action = array(
		'move'	=> $user->lang('move_files'),
		'delete'	=> $user->lang('delete')
	);
	
	$folder = $uploader->file_tree($pfh->FolderPath('files','eqdkp'), '', array(), true, true, true);
	$dropdown['/'] = 'files';
	foreach ($folder as $key => $value){
		$dropdown[str_replace($pfh->FolderPath('files','eqdkp').'/', "", $key)] = '&nbsp;&nbsp;'.$value;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#eqdkp_uploader_dlg.title}</title>
  <script type="text/javascript" src="<?php echo get_const('root_path'); ?>libraries/jquery/core/core.js"></script>
	<script type="text/javascript" src="../../tiny_mce_popup.js?v={tinymce_version}"></script>
  <script type="text/javascript" src="../../utils/mctabs.js?v={tinymce_version}"></script>
	<script type="text/javascript" src="js/dialog.js?v={tinymce_version}"></script>
  <script type="text/javascript">
	function insertFile(name)	{
		name = replace_url(name);
		try {
			if (is_image(name)){			
				image = true;
			} else {
				
				image = false;
			}

		} catch(e) {
			alert("Error");
		}
		
		Eqdkp_uploaderDialog.insert(name, image);
	}
	
	function is_image(file_name) {
  // Die erlaubten Dateiendungen
  var image_extensions = new Array('jpg', 'jpeg','gif','png');

  // Dateiendung der Datei
  var extension = file_name.split('.');
  extension = extension[extension.length - 1];
  extension = extension.toLowerCase();
  for (var k in image_extensions) {
    if (image_extensions[k] == extension) return true;
  }
  return false;
}

function replace_url(string){

	return string.replace("<?php echo get_const('root_path'); ?>","<?php echo register('environment')->link; ?>");
}

function check_action_dropdown(){
		
	var action = document.getElementById('action_drpdwn').value;

	if (action == "move"){
			
		document.getElementById('target_dd').style.display = "inline";
			
	} else {
			
		document.getElementById('target_dd').style.display = "none";
			
	}
}
</script>
</head>
<body>
<style>
<?php  echo $uploader->add_css();?>
</style>

<form action="dialog.php" enctype="multipart/form-data" method="post">

<div class="tabs">
			<ul>
				<li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onmousedown="return false;"><?php echo $user->lang('file_manager'); ?></a></span></li>
				<li id="upload_tab"><span><a href="javascript:mcTabs.displayTab('upload_tab','upload_panel');" onmousedown="return false;"><?php echo $user->lang('upload_file'); ?></a></span></li>
				<li id="folder_tab"><span><a href="javascript:mcTabs.displayTab('folder_tab','folder_panel');" onmousedown="return false;"><?php echo $user->lang('add_folder'); ?></a></span></li>
			</ul>
		</div>

		<div class="panel_wrapper">
			<div id="general_panel" class="panel current">
      <h2><?php echo $user->lang('file_manager'); ?></h2>
        <?php echo $uploader->file_tree($pfh->FolderPath('files', 'eqdkp'), 'javascript:insertFile(\'[link]\');', array(), true, false, false, true);?>
  <div class="mceActionPanel">
  	
    <div style="float: left"><?php echo $user->lang('selected_files'); ?> <?php echo $html->DropDown('action', $action, 'move', '', 'onChange="check_action_dropdown()"', 'input', 'action_drpdwn'); ?><span id="target_dd"> <?php echo $user->lang('move_to'); ?> <?php echo $html->Dropdown('dest_folder', $dropdown, array()); ?></span>  
      <div style="clear:both;"></div>
	  <input type="submit" value="<?php echo $user->lang('go'); ?>" id="insert">
      <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right"></div>
</div>
      
      </div>
      
      
      
      <div id="upload_panel" class="panel">
         <div id="table_upload">
        <h2><?php echo $user->lang('upload_file'); ?></h2>
        <p><?php echo $user->lang('select_dest_folder'); ?>: <?php echo $html->Dropdown('folder', $dropdown, array()); ?></p>
        <p><?php echo $user->lang('select_file'); ?>: <input type="file" name="upload" /></p>
        <div class="mceActionPanel">
            <div style="float: left"><input type="submit" id="insert" name="upload" value="<?php echo $user->lang('go'); ?>" /></div>
            <div style="float: right"><input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" /></div>
        </div>
        <div style="clear:both;"></div>
        </div>
      </div>
      
      
      <div id="folder_panel" class="panel">
      
      
        <div id="table_folder">
        <h2><?php echo $user->lang('add_folder'); ?></h2>
         <p><?php echo $user->lang('select_dest_folder'); ?>: <?php echo $html->Dropdown('src_folder', $dropdown, array()); ?></p>
        <p><?php echo $user->lang('folder_name'); ?>:
          <input type="text" name="name" /></p>
        <div class="mceActionPanel">
            <div style="float: left"><input type="submit" id="insert" name="create_folder" value="<?php echo $user->lang('go'); ?>" /></div>
            <div style="float: right"><input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" /></div>
        </div>
        <div style="clear:both;"></div>
        </div>
      </div>
      
     </div>

 


</form>

</body>
</html>
<?php } else {?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#eqdkp_lightbox_dlg.title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js?v={tinymce_version}"></script>
	<script type="text/javascript" src="js/dialog.js?v={tinymce_version}"></script>
</head>
<body>
Access denied.
</body>
</html>
<?php } ?>