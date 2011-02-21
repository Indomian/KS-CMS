tinyMCEPopup.requireLangPack();

var ksSmileDialog = {
	init : function() {
		tinymce.util.XHR.send({
			   url : '/index.php?type=AJAX&module=interfaces&action=SmiliesList',
			   success : function(text) {
			      document.getElementById('smiliesPanel').innerHTML=text;
			   }
			});

		tinymce.dom.Event.add(document, 'click', function(e) {
			   if(e.target.tagName=='IMG')
			   if(e.target.getAttribute('alt')!='')
			   {
				   var code='<img _mce_ks_smile="'+e.target.getAttribute('alt')+'" src="'+e.target.src+'"/>';
				   tinyMCEPopup.editor.execCommand('mceInsertContent', false, code);
				   tinyMCEPopup.close(); 
			   }
		});
	},
};

tinyMCEPopup.onInit.add(ksSmileDialog.init, ksSmileDialog);
