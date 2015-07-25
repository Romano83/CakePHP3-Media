<?php
$sizes = getimagesize(WWW_ROOT . trim($media->file, '/'));
$thumb = ($thumbID && $media->id === $thumbID) ? 'is-thumbnail' : '';
$about = __d('media', 'About this file');
$editPicture = __d('media', 'Modify picture');
$basename = basename($media->file);
$title = __d('media', 'Title');
$name = pathinfo($media->file, PATHINFO_FILENAME);
$alt = __d('media', 'Alt text');
$target = __d('media', 'Target');
$caption = __d('media', 'Caption');
$settings = __d('media', "Display settings");
$aligment = __d('media', 'Alignment');
$none = __d('media', 'None');
$center = __d('media', 'Center');
$left = __d('media', 'Left');
$right = __d('media', 'Right');
$btn1 = ($thumbID !== false && $media->id !== $thumbID && $media->file_type == 'pic') ? $this->Html->link(__d('media', "Set as thumbnail"), [
    'action' => 'thumb',
    $media->id
], [
    'class' => 'btn btn-default'
]) : '';
$btn2 = ($editor) ? "<a href='#' class='submit btn btn-primary'>" . __d('media', 'Insert into post') . "</a>" : '';

$json['content'] = "<div class=\"gallery-item {$thumb}\" id=\"gallery-{$media->id}\" data-id=\"{$media->id}\">
	<div class=\"gallery-item-thumb\">
		{$this->Html->image($media->file_icon)}
	</div>
	<div class=\"gallery-item-infos\">
		<h3>{$about}</h3>
		<div class=\"item-details\">
			<div class=\"file\">
				{$this->Html->image($media->file_icon)}
			</div>
			<div class=\"details\">
				<span class=\"file-title\"><strong>{$basename}</strong></span>
				<span class=\"file-dimension\">{$sizes[0]} x {$sizes[1]}</span>
				<a href=\"#\" class=\"edit-file\">{$editPicture}</a>
				{$this->Html->link(__d('media','Delete definitively'), ['action'=>'delete',$media->id], ['class'=>'delete red'])}
			</div>
		</div>
		{$this->Form->create($media, ['url' => ['controller' => 'medias', 'action' => 'update', $media->id]])}
			<label class=\"settings\">
				<span>{$title}</span>
				{$this->Form->input('name', ['class' => 'title autosubmit', 'div' => false, 'label' => false, 'value' => $name])}
			</label>
			<label class=\"settings\">
				<span>{$alt}</span>
				<input class=\"alt\" name=\"alt\" type=\"text\">
			</label>
			<label class=\"settings\">
				<span>{$caption}</span>
				{$this->Form->input('caption', ['class' => 'caption autosubmit', 'div' => false, 'label' => false])}
			</label>
			<label class=\"settings\">
				<span>{$target}</span>
				<input class=\"href\" name=\"href\" type=\"text\" disabled value=\"{$this->Url->build($media->file)}\">
			</label>
			<h3>{$settings}</h3>
			<div class=\"settings media-alignment\">
				<span>{$aligment}</span>
				<select name=\"align\" class=\"align\">
					<option value=\"none\">{$none}</option>
					<option value=\"center\">{$center}</option>
					<option value=\"left\">{$left}</option>
					<option value=\"right\">{$right}</option>
				</select>
			</div>
			<input type=\"hidden\" class=\"filetype\" name=\"filetype\" value=\"{$media->file_type}\" />
			<input type=\"hidden\" name=\"file\" value=\"{$this->Url->build($media->file)}\" class=\"path\">
		{$this->Form->end()}
		<p class=\"tright\">
			{$btn1}
			&nbsp;
			{$btn2}
		</p>
	</div>
</div>";

echo json_encode($json);
?>