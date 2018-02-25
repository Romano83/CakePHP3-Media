function initTinymce(config) {
    jQuery(function($){
        if( $('#explorer').length === 0 ){
            buttons = buttons.replace('image,','');
        }
        var options = {
            language: "fr_FR",
            relative_urls: false,
            skin: 'lightgray',
            height: 500,
            plugins: [
                "autoresize autolink autosave link lists charmap hr spellchecker",
                "searchreplace wordcount visualblocks visualchars code media nonbreaking",
                "contextmenu template textcolor paste"
            ],
            toolbar1: "bold italic underline strikethrough blockquote bullist numlist alignleft aligncenter alignright alignjustify link unlink image",
            toolbar2: "styleselect outdent indent removeformat charmap undo redo code",
            toolbar3: "",
            menubar: false,
            toolbar_items_size: 'medium',
            paste_as_text: true,
            autoresize_max_height: 700
        };

        jQuery.extend(options, config);

        var requiredOptions = {
            selector: 'textarea.tinymce',
			plugins : [
				"gallery"
			],
            gallery_explorer: $('#explorer').val() + '?editor=tinymce&id=' + $(this).attr('id'),
            gallery_edit: $('#edit').val() + '?editor=tinymce&id=' + $(this).attr('id'),
		};

        jQuery.extend(options, requiredOptions);

        $('textarea.tinymce').each(function(){
            tinymce.init(options);
        });
    });
}
