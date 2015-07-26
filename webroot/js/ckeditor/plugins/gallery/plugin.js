CKEDITOR.plugins.add('gallery', {

   requires: ['iframedialog'],
   
   lang : ['en'],
   
   init : function(editor){
       var pluginName = 'gallery';
       
       CKEDITOR.dialog.add('gallery', this.path + 'dialogs/gallery.js');
       
       editor.addCommand('gallery', new CKEDITOR.dialogCommand('gallery', {
	   allowedContent : 'img[!src,alt,class,data-id]; figure; figcaption',
       }));
       
       editor.ui.addButton('gallery', {
	  label : editor.lang.gallery.gallery.label,
	  command : pluginName,
	  icon : this.path + 'images/gallery.png'
       });
   }
    
});