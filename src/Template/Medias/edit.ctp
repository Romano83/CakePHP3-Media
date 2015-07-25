<div class="media-content">
	<div class="media-settings">
		<div class="column-image">
			<div class="image">
				<?= $this->Html->image($data['src'], ['class' => 'path']); ?>
				<div class="actions">
					<button class="btn btn-default"><?= __d('media', 'Modify picture'); ?></button>
					<?= $this->Html->link(__d('media', 'Replace picture'), ['controller' => 'medias', 'action' => 'index', $data['ref'], $data['ref_id'], '?' => ['editor' => $data['editor']]], ['class' => 'btn btn-default']); ?>
				</div>
			</div>
		</div>
		<div class="column-settings">
			<label class="settings"> <span><?= __d('media',"Title"); ?></span> <input
				type="text" class="title" name="title"
				value="<?= basename($data['src']); ?>">
			</label> <label class="settings"> <span><?= __d('media',"Alt text"); ?></span>
				<input class="alt" name="alt" type="text"
				value="<?= $data['alt']; ?>">
			</label> <label class="settings"> <span><?= __d('media',"Caption"); ?></span>
				<textarea class="caption" name="caption"><?= $data['caption']; ?></textarea>
			</label> <input type="hidden" class="filetype" name="filetype"
				value="<?= $data['type']; ?>" />
			<h3><?= __d('media', "Display settings"); ?></h3>
			<div class="settings media-alignment">
				<span><?= __d('media',"Alignment"); ?></span>
				<div class="btn-group" role="group">
					<button type="button"
						class="btn btn-default <?= $data['class'] == 'alignleft' ? 'active' : '' ?>"
						value="left"><?= __d('media','Left'); ?></button>
					<button type="button"
						class="btn btn-default <?= $data['class'] == 'aligncenter' ? 'active' : '' ?>"
						value="center"><?= __d('media','Center'); ?></button>
					<button type="button"
						class="btn btn-default <?= $data['class'] == 'alignright' ? 'active' : '' ?>"
						value="right"><?= __d('media','Right'); ?></button>
					<button type="button"
						class="btn btn-default <?= $data['class'] == 'align' || $this->request->query['class'] == '' ? 'active' : '' ?>"
						value="none"><?= __d('media','None'); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="media-footer">
	<div class="media-footer-inner">
		<div class="media-footer-secondary"></div>
		<div class="media-footer-primary">
			<a href="#" class="update btn btn-primary btn-media"><?= __d('media',"Update"); ?></a>
		</div>
	</div>
</div>


<?= $this->Html->scriptStart(['block' => 'mediaScriptBottom']); ?>

(function($){
	$('body').on('click', '.btn-group .btn', function(e){
		var $this = $(this);
		$this.siblings().removeClass('active');
		$this.addClass('active');
	});

	<?php if($this->request->query['editor']): ?>
		$('.media-footer').on('click', 'a.update', function(e){
			e.preventDefault();
			var $this = $(this);
			var html = createHtmlElement($this);
			var win = (!window.frameElement && window.dialogArguments) || opener || parent || top;
			win.update_to_<?= $this->request->query['editor']; ?>(html, window);
			return false;
		});

		function createHtmlElement($this) {
			var item = $this.parents('.media-footer').siblings('.media-content');
			var type = $('.filetype', item).val();
			if(type === 'pic') {				

				var img = '<img src="'+$('.path', item).attr('src')+'"';
				
				if( $('.alt', item).val() !='' ){
					img +=' alt="' + $('.alt', item).val() + '"';
				}
				if( $('.btn.active', item).val() !='none'){
					img +=' class="align' + $('.btn.active', item).val() + '"';
				}
				img +=' data-id="<?= $data['media_id']; ?>"/>';
				
				if( $('.caption', item).val() !='' ){
					var	figStart='<figure> ';
					var figEnd='</figure> ';
					var	caption='<figcaption>"' + $('.caption', item).val() + '"</figcaption>'; 
					var html = figStart + ' <a href="'+$('.path', item).attr('src')+'" class="zoombox"	title="'+$('.title', item).val()+'">' + img + '</a>' + caption + figEnd; 
				} else { 
					if( $('.href', item).val() != '' ){ 
						html = '<a href="'+$('.path', item).attr('src')+'" class="zoombox"	title="'+$('.title', item).val()+'">' + img + '</a> '; 
					} 
				} 
			} else { 
				html = ' <a href="'+$('.path', item).attr('src')+'" class="zoombox"	title="'+$('.title', item).val()+'">' + $('.title', item).val() + '</a>';
			}
			return html;
		}

	<?php endif; ?>

})(jQuery);

<?= $this->Html->scriptEnd(); ?>