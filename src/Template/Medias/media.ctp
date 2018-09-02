<?php
    if (file_exists(WWW_ROOT.trim($media->file, '/'))) {
	    $sizes = getimagesize(WWW_ROOT.trim($media->file, '/'));
    }
?>

<div
	class="gallery-item <?php if($thumbID && $media->id === $thumbID): ?>is-thumbnail<?php endif; ?>"
	id="gallery-<?= $media->id; ?>" data-id="<?= $media->id; ?>">
    <div class="gallery-item-thumb">
        <?= $this->Html->image($media->file_icon); ?>
	</div>
	<div class="gallery-item-infos">
		<h3><?= __d('media','About this file'); ?></h3>
		<div class="item-details">
			<div class="file">
				<?= $this->Html->image($media->file_icon);?>
			</div>
			<div class="details">
				<span class="file-title"><strong><?= basename($media->file); ?></strong></span>
				<?php if($media->file_type == 'pic'): ?>
                    <span class="file-size"><?= $this->Number->toReadableSize(filesize(WWW_ROOT.trim($media->file, '/'))); ?></span>
				    <span class="file-dimension"><?= $sizes[0].' x '.$sizes[1]; ?></span>
                    <?= $this->Html->link(__d('media','Modify picture'), ['action' => 'edit', $media->id], ['class' => 'edit-file']) ?>
				<?php endif; ?>
				<?= $this->Html->link(__d('media','Delete definitively'), ['action'=>'delete',$media->id], ['class'=>'delete red']); ?>
			</div>
		</div>
		<?= $this->Form->create($media, ['url' => ['controller' => 'medias', 'action' => 'update', $media->id]]); ?>
			<label class="settings"> <span><?= __d('media',"Title"); ?></span>
				<?= $this->Form->input('name', ['class' => 'title autosubmit', 'div' => false, 'label' => false, 'value' => $media->name ? $media->name : pathinfo($media->file, PATHINFO_FILENAME)]); ?>
			</label>
			<?php if($media->file_type == 'pic'): ?>
			<label class="settings"> <span><?= __d('media',"Alt text"); ?></span>
			<input class="alt" name="alt" type="text">
		</label>
			<?php endif; ?>
			<label class="settings"> <span><?= __d('media',"Caption"); ?></span>
				<?= $this->Form->input('caption', ['class' => 'caption autosubmit', 'div' => false, 'label' => false]); ?>
			</label> <label class="settings"> <span><?= __d('media',"Target"); ?></span>
			<input class="href" name="href" type="text" disabled
			value="<?= $this->Url->build($media->file); ?>">
		</label>
			<?php if($media->file_type == 'pic'): ?>
			<h3><?= __d('media', "Display settings"); ?></h3>
		<div class="settings media-alignment">
			<span><?= __d('media',"Alignment"); ?></span> <select name="align"
				class="align">
				<option value="none"><?= __d('media','None'); ?></option>
				<option value="center"><?= __d('media','Center'); ?></option>
				<option value="left"><?= __d('media','Left'); ?></option>
				<option value="right"><?= __d('media','Right'); ?></option>
			</select>
		</div>
			<?php endif; ?>
			<input type="hidden" class="filetype" name="filetype"
			value="<?= $media->file_type; ?>" /> <input type="hidden" name="file"
			value="<?= $this->Url->build($media->file); ?>" class="path">	
		<?= $this->Form->end(); ?>
		<p class="tright">
			<?php if($thumbID !== false && $media->id !== $thumbID && $media->file_type == 'pic'): ?>
				<?= $this->Html->link(__d('media',"Set as thumbnail"), ['action'=>'thumb',$media->id], ['class' => 'btn btn-default']); ?>
			<?php endif; ?>
			<?php if ($editor): ?>
				<a href="" class="submit btn btn-primary"><?= __d('media',"Insert into post"); ?></a>
			<?php endif; ?>
		</p>
	</div>
</div>
