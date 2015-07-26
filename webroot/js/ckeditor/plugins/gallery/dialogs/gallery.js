var myEditorForMedia = null;
var myEditorDialog = null;

function send_to_ckeditor(content, win){
    myEditorForMedia.insertHtml(content);
    CKEDITOR.dialog.getCurrent().hide()
}

CKEDITOR.dialog.add('gallery', function(editor)
{
    myEditorForMedia = editor;
    return {
	title : editor.lang.gallery.gallery.title,
	minWidth : window.innerWidth - 100,
	minHeight : window.innerHeight - 100,
	buttons : [],
	contents :
	[
		{
			id : 'iframe',
			label : '',
			expand : true,
			elements :
			[ {
				type   : 'iframe',
				width  : '100%',
				height : window.innerHeight - 100,
				src    : $('#explorer').val() + '?editor=ckeditor&id=' + editor.name ,
			} ]
		},
	]
    };
});
