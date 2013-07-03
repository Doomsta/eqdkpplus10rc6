tinyMCEPopup.requireLangPack();

var PagesDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		f.urlname.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
	},

	insert : function() {
		// Insert the contents from the input into the document
		page = document.forms[0].page.value;
		
		name = document.forms[0].urlname.value;
		if (name != ""){
			output = '<a href="{{page_url::'+page+'}}">'+name+'</a>';
		} else {
			output = '{{page::'+page+'}}';
		}
		
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, output);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(PagesDialog.init, PagesDialog);
