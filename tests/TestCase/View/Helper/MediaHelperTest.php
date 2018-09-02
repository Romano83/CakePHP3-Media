<?php
namespace Media\Test\TestCase\View\Helper;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Media\View\Helper\MediaHelper;

/**
 * Media\View\Helper\MediasHelper Test Case
 */
class MediaHelperTest extends TestCase
{

    public $helper = [
        'Html',
        'Form',
        'Url'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Media = new MediaHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Media);
        
        parent::tearDown();
    }

    /**
     * Test testTinymceRender
     *
     * @return void
     */
    public function testTinymceRender()
    {
        $result = $this->Media->tinymce('content', 'Posts', 1);
        $this->assertContains('<textarea name="content" style="width:100%;height:500px" rows="160" class="wysiwyg tinymce" id="content"></textarea>', $result);
        $this->assertContains('<input type="hidden" id="explorer" value="/media/medias/index/Posts/1">', $result);
        $this->assertContains('<input type="hidden" id="edit" value="/media/medias/edit/">', $result);
    }

    /**
     * Test testCkeditorRender
     *
     * @return void
     */
    public function testCkeditorRender()
    {
        $result = $this->Media->ckeditor('content', 'Posts', 1);
        $this->assertContains('<textarea name="content" style="width:100%;height:500px" rows="160" class="wysiwyg ckeditor" id="content"></textarea>', $result);
        $this->assertContains('<input type="hidden" id="explorer" value="/media/medias/index/Posts/1">', $result);
        $this->assertContains('<input type="hidden" id="edit" value="/media/medias/edit/">', $result);
    }

	/**
	 * Test testImageRender
	 *
	 * @return void
	 */
	public function testImageRender() {
		$result = $this->Media->image(WWW_ROOT . 'img' . DS . 'upload' . DS . date('Y') . DS . date('m') . DS . 'imageHelper.png');
		$this->assertContains('<img src="'.WWW_ROOT . 'img' . DS . 'upload' . DS . date('Y') . DS . date('m') . DS . 'imageHelper.png'.'" alt=""/>', $result);
    }

	/**
	 * Test testImageResizedRender
	 *
	 * @return void
	 */
    public function testImageResizedRender() {
	    new Folder(WWW_ROOT . 'img' . DS . 'upload' . DS . date('Y') . DS . date('m'), true, 0777);
	    $resizedFile = new File(TMP . 'testHelper_50x50.jpg');
	    $resizedFile->copy(WWW_ROOT . 'img' . DS . 'upload' . DS . date('Y') . DS . date('m') . DS . 'imageHelper_50x50.png');
	    $result = $this->Media->image(WWW_ROOT . 'img' . DS . 'upload' . DS . date('Y') . DS . date('m') . DS . 'imageHelper.png', ['width' => 50, 'height' => 50]);
	    $this->assertContains('<img src="'.WWW_ROOT . 'img' . DS . 'upload' . DS . date('Y') . DS . date('m') . DS . 'imageHelper_50x50.png'.'" width="50" height="50" alt=""/>', $result);
    }

    /**
     * Test testIframeRender
     *
     * @return void
     */
    public function testIframeRender()
    {
        $result = $this->Media->iframe('Posts', 1);
        $this->assertContains('<iframe src="/media/medias/index/Posts/1" style="width:100%;min-width:600px;" id="medias-Posts-1"></iframe>', $result);
    }
}
