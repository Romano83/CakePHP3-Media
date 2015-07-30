<?php use Cake\Routing\Router ; ?>
<div id="plupload">

	<ul class="tabs">
		<li id="browse-tab" class="tab-item"><a href="#browse"
			data-toggle="tab"><?php echo __d('media','Upload a new file'); ?></a></li>
		<li id="gallery-tab" class="tab-item active"><a href="#gallery"
			data-toggle="tab"><?= __d('media','Gallery'); ?></a></li>
	</ul>

	<div class="tab-content">
		<div id="browse" class="tab-pane browse">
			<span><?php echo __d('media',"Drop your files to upload"); ?></span>
		</div>
		<div id="gallery" class="tab-pane gallery active in">
			<?php foreach ($medias as $media):?>
				<?php require('media.ctp'); ?>
			<?php endforeach ?>
			<div class="cb"></div>
		</div>
	</div>

	<div class="overlay">
		<div class="borders">
			<span><?php echo __d('media',"Drop your files to upload"); ?></span>
		</div>
	</div>

</div>

<div id="loader"></div>

<div id="error-modal" class="modal">
	<div class="modal-icon">
		<i class="fa fa-warning"></i>
	</div>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert"
			aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<div class="modal-body"></div>
	</div>
</div>

<div id="template" class="preview-item" style="display: none">
	<div class="preview-item-thumb progress-container">
		<div class="progress" style="display: none">
			<div class="progress-bar" role="progressbar" aria-valuenow="60"
				aria-valuemin="0" aria-valuemax="100"></div>
		</div>
	</div>
</div>



<?= $this->Html->script('/media/js/underscore.min.js', ['block' => 'mediaScriptBottom']); ?>
<?= $this->Html->script('/media/js/dropzone.js', ['block' => 'mediaScriptBottom']); ?>
<?= $this->Html->script('/media/js/tab.js', ['block' => 'mediaScriptBottom']); ?>
<?= $this->Html->script('/media/js/modal.js', ['block' => 'mediaScriptBottom']); ?>
<?= $this->Html->scriptStart(['block' => 'mediaScriptBottom']); ?>

(function($){
	var timer = null, 
		$loader = $('#loader'), 
		$template = $('#template'),
		$gallery = $('#gallery'),
		$overlay = $('.overlay'),
		$errorModal = $('#error-modal');
	dragEnteredEls = [];
	
	// Dropzone
	var drop = $("body").dropzone({
		url: "<?= Router::url(['controller'=>'medias','action'=>'upload',$ref,$refId,'?' => [ "id" => $id, 'editor'=>$editor, ] ]); ?>",
		//acceptedFiles: ".<?= implode(';.', $extensions); ?>",
		clickable: "#browse",
		previewTemplate: false,
		dragenter: function (e) {
		    dragEnteredEls.push(e.target);
		    $overlay.stop().fadeIn();
		    
		},
		drop: function (e) {
		    $overlay.stop().fadeOut();
		},
		dragleave: function (e) {
		    dragEnteredEls = _.without(dragEnteredEls, e.target);
		    if (dragEnteredEls.length === 0) {
		    	$overlay.stop().fadeOut();
		    }
		},
		addedfile: function(file){
			$template.clone().prependTo('#gallery').show();
			$('#gallery-tab').tab('show');
			$('#browse').removeClass('active in');
			$gallery.addClass('active in');
		},
		success: function (file, data) {
			if(data.error){
				$errorModal.on('show.bs.modal', function(e){
					$(this).find('.modal-body').text(data.error.file.global);
				});
				$errorModal.modal({backdrop: false});
				removeTemplate($gallery, '#template');
			}else{
				removeTemplate($gallery, '#template');
				$item = $($.parseJSON(data).content);
				$item.addClass('is-active');
				$('.gallery-item-infos').hide();
				$('.gallery-item-infos', $item).show();
				$('.gallery').prepend($item);
			}
		},
		uploadprogress: function(file, percent) {
			$('.progress').show();
			$('.progress-bar').css('width', percent +"%");
		},
	});

	function removeTemplate(selector, element){
		var templates = selector.find(element);
		for(i=0; templates.length>i; i++){
			templates.fadeOut().remove();
		}
	}

	$('body').on('click', function(e){
		if($errorModal.length>0){
			$errorModal.modal('hide');
		}
	});

	// Order
	$('.gallery').sortable({
		items: '.gallery-item',
		handle: '.gallery-item-thumb',
		update: function(event, ui) {
    	var order = $(this).sortable("toArray", {attribute: "data-id"});
    	var ids = {};
    	for(var i in order){
    		ids[order[i]] = i;
    	}
    	$loader.stop().fadeIn();
    	$.post('<?= $this->Url->build(['action' => 'order']); ?>', {Media: ids}, function(data){
    		$loader.stop().fadeOut();
    	});
    }
	});

	$('.gallery').disableSelection();

	// Clicks on items to reveal details
	$('.gallery').on('click', '.gallery-item-thumb', function(e){
		e.preventDefault();
		$item = $(this).parent();
		$item.addClass('is-active').siblings().removeClass('is-active');
		$('.gallery-item-infos').hide();
		$('.gallery-item-infos', $item).show();
	});
	$('.gallery').on('submit', 'form', function(e){
		datas = $(this).serialize();
		$loader.stop().fadeIn();
		$.post($(this).attr('action'), datas, function(data){
			$loader.fadeOut();
		});
		e.preventDefault();
		return false;
	});
	// Delete link
	$('.gallery').on('click', '.delete', function(e){
		e.preventDefault();
		if (confirm("<?= __d('media','Do you really want to delete this file ?'); ?>")) {
			$this = $(this);
			$.get($(this).attr('href'), {}, function(){
				$this.parents('.gallery-item').fadeOut();
			});
		}
	});
	$('.gallery').on('blur', '.autosubmit', function(){
		$(this).parents('form').trigger('submit');
	});

	$('body').on('click', '.close', function(e){
		e.preventDefault();
		$(this).parents('#error-modal').modal('hide');
	})


	<?php if($editor): ?>
		$('.gallery').on('click', 'a.submit', function(e){
			e.preventDefault();
			var $this = $(this);
			var html = createHtmlElement($this);
			var win = (!window.frameElement && window.dialogArguments) || opener || parent || top;
			win.send_to_<?= $editor; ?>(html, window, "<?= $id; ?>");
			return false;
		});

		function createHtmlElement($this) {
			var item = $this.parents('.gallery-item');
			var type = $('.filetype', item).val();
			if(type === 'pic') {				

				var img = ' <img src="'+$('.path', item).val()+'"	';
				if( $('.alt', item).val() !='' ){
					img +=' alt="' + $('.alt', item).val() + '"';
				}
				if( $('.align', item).val() !='none'){
					img +=' class="align'	+ $('.align', item).val() + '"';
				}
				img +=' data-id="' + item.data('id')+ '" />'; 
				
				if( $('.caption', item).val() != ''){ 
					
					var figStart = '<figure>'; 
					var figEnd = '</figure>'; 
					var caption = '<figcaption>"' + $('.caption', item).val() + '"</figcaption>'; 
					
					if( $('.href', item).val() != '' ){ 
						html = figStart + '<a href="'+$('.href', item).val()+'" class="zoombox"	title="'+$('.title', item).val()+'">' + img + '</a>' + caption + figEnd; 
					} 
				} else { 
					if( $('.href', item).val() != '' ){ 
						html ='<a href="'+$('.href', item).val()+'" class="zoombox"	title="'+$('.title', item).val()+'">' + img + '</a>'; 
					} 
				} 
			} else { 
				var title = ''; 
				if($('.title', item).val() == ''){ 
					title = $('.file-title', item).text(); }else{ title = $('.title', item).val();
				} 
			html = ' <a href="'+$('.href', item).val()+'" class="zoombox" title="'+title+'">' + title + '</a>';
		}
			return html;
		}

	<?php endif; ?>

})(jQuery);

<?= $this->Html->scriptEnd(); ?>



