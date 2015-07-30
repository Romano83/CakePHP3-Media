# Media plugin for CakePHP 3

## UPDATE 2015-07-30
**WARNING**

If you download this plugin before this date, you can have this error : 
>*Error: The application is trying to load a file from the Media plugin. aroundMake sure your plugin Media is in the /your/path/to/app/plugins/ directory and was loaded.*

To fix this error, you **must** run `composer remove romano83/cakephp3-media` and `composer require romano83/cakephp3-media`. Sorry for this problem and I hope you can enjoy this plugin !

## About

The aim of this plugin is to give you the ability to create and associate any kind of medias in your application.
*This plugin is the adapation from [Grafikart's Media Plugin](https://github.com/Grafikart/CakePHP-Media) for CakePHP 3.*

### Overview

* BelongsTo and hasMany Media association for you model;
* Upload using drag & drop based on [dropzone.js](https://github.com/enyo/dropzone/);
* Image uploader integrated with [tinymce 4.2](https://github.com/tinymce/tinymce) and [CKEditor 4.5](https://github.com/ckeditor/ckeditor-dev)


## Installation

### Requirements
* [composer](http://getcomposer.org)
* [CakePHP 3.*](https://github.com/cakephp/cakephp)

### Steps to install

* Run :
```
composer require romano83/cakephp3-media
```
* Import The file `config\schema\medias.sql` in your database


## How to use

In your `config\bootstrap.php` file, add this line 
```php
Plugin::load('Media', ['bootstrap' => false, 'routes' => true]);
``` 

### Security purpose

By default, the plugin is blocked for everyone. To set proper permissions, you can implement *canUploadMedia()* method in your `YourApp\AppController`.
For exemple :
```php
public function canUploadMedias($model, $id){
	if($model === 'YourApp\Model\Table\UsersTable' && $id == $this->Auth->user('id')){
		return true; // Everyone can upload medias for their own records
	}
	return $this->Auth->user('role') == 'admin'; // Admins have all rights
}
```

### Behavior

To use this plugin, you must load the `MediaBehavior` in your tables. Form instance, if you want to manage medias for your posts :
```php
<?php
namespace MyApp\Model\Table;

use Cake\ORM\Table;

class PostsTable extends Table
{
	public function initialize(array $config)
	{
		$this->addBehavior(
			'Media.Media', [
				'path' => 'img/upload/%y/%m/%f', 	// default upload path relative to webroot folder (see below for path parameters)
				'extensions' => ['jpg', 'png'],  	// array of authorized extensions (lowercase)
				'limit' => 0,						// limit number of upload file. Default: 0 (no limit)
				'max_width' => 0,					// maximum authorized width for uploaded pictures. Default: 0 (no limitation) 
				'max_height' => 0,					// maximum authorized height for uploaded pictures. Default: 0 (no limitation)
				'size' => 0							// maximum autorized size for uploaded pictures (in kb). Default: 0 (no limitation)
			]
		);
	}
}
```
For the path option, you have numerous parameters :
* %y		Year
* %m		Month
* %f		Sluggified filename
* %id		Media Id
* %cid		Media Id /100
* %mid		Media Id /1000

### Helper

In order to add media upload and edit capabilities in your views, you can use `MediaHelper`.

To do so, add in your `AppController` 
```php
<?php
namespace App\Controller;

class AppController extends Controller
{
	public $helpers = ['Media.Media'];
}
```
Or in your `AppView`
```php
<?php
namespace App\View;

class AppView extends View
{
	public function initialize()
	{
		$this->loadHelper( 'Media.Media' );
	}
}
```

For example, you can add an iframe to manage medias: 
```php
<?= $this->Media->iframe('Model', 'ID'); ?>
```
Or you can add text editor (tinymce for instance) inside form :
```php
<?= $this->Form->create(); ?>
<?= $this->Media->tinymce($fieldname, 'Model', 'Id', array $options); ?>
<?= $this->Form->end(); ?>
```
This method take the same `array $options` than native `FormInput` helper.

### Thumb

If you want to add thumb for your posts, you must add `media_id` field in your 'Model' database table.

#### Notice

In order to upload pictures, you should implement **draft system** for your model.


## ToDo
* Add the ability to edit a media when clicked;
* Add resize, crop, flip and rotate functions;
* Finish unit test...


## How to contribute
* You have find a bug ? You can open an [issue](https://github.com/Romano83/CakePHP3-Media/issues/new)
	* Clearly describe the issue including steps to reproduce when it is a bug.
	* Make sure you fill in the earliest version that you know has the issue.
	* Screenshots and code exemple are welcome in the issues.
* You want to implement a new feature or fix a bug ? Please follow this guide :
	* Your code **must follow** the [Coding Standard of CakePHP](http://book.cakephp.org/3.0/en/contributing/cakephp-coding-conventions.html). Check the [cakephp-codesniffer](https://github.com/cakephp/cakephp-codesniffer) repository to setup the CakePHP standard.
	* You must **add Test Cases** for your new feature. Test Cases ensure that the application will continue to working in the future.
	* Your PR should be on the `dev` branch.

## Special thanks

   * [Grafikart](https://github.com/Grafikart) for the first version of this plugin !

