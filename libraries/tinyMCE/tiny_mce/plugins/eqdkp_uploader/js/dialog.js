tinyMCEPopup.requireLangPack();

var Eqdkp_uploaderDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		$(document).ready( function() {
				
				// Hide all subfolders at startup
				$(".php-file-tree").find("UL").hide();
				
				// Expand/collapse on click
				$(".pft-directory a").click( function() {

					$(this).parent().find("UL:first").slideToggle("medium");
					if( $(this).parent().attr('className') == "pft-directory" ) return false;
				});
			
			});

	},

	insert : function(name, image) {
		// Insert the contents from the input into the document
		if (image){
			output = '<img src="'+name+'" alt="Image" />';
		} else {
			output = '<a href="'+name+'">'+name+'</a>';
		}
		
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, output);
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.resizeToInnerSize();
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(Eqdkp_uploaderDialog.init, Eqdkp_uploaderDialog);
