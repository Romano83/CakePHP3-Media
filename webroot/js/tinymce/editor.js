jQuery(function($){
	var buttons = "bold italic underline strikethrough blockquote bullist numlist alignleft aligncenter alignright alignjustify link unlink image";
	if( $('#explorer').length == 0 ){
		buttons = buttons.replace('image,','');
	}
	$('textarea.tinymce').each(function(){
		tinymce.init({
			selector: 'textarea.tinymce',
			language: "fr_FR",
			relative_urls: false,
			skin: 'lightgray',
			height: 500,
			plugins: [
		            "autoresize autolink autosave link gallery lists charmap hr spellchecker",
		            "searchreplace wordcount visualblocks visualchars code media nonbreaking",
		            "contextmenu template textcolor paste"
		        ],

		    toolbar1: buttons,
		    toolbar2: "styleselect outdent indent removeformat charmap undo redo code",
		    toolbar3: "",
		    menubar: false,
		    toolbar_items_size: 'medium',
		    paste_as_text: true,
		    gallery_explorer: $('#explorer').val() + '?editor=tinymce&id=' + $(this).attr('id'),
		    gallery_edit: $('#edit').val() + '?editor=tinymce&id=' + $(this).attr('id'),
		    autoresize_max_height: 700,
		
		});		
	});	
});