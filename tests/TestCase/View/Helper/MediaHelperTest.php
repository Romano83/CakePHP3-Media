<?php
namespace Media\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Media\View\Helper\MediaHelper;

/**
 * Media\View\Helper\MediasHelper Test Case
 */
class MediaHelperTest extends TestCase
{
    
    public $helper = ['Html', 'Form', 'Url'];

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
     * Test testIframeRender
     *
     * @return void
     */
    public function testIframeRender()
    {
        $result = $this->Media->iframe('Posts', 1);
        $this->assertContains('<iframe src="/media/medias/index/Posts/1" style="width:100%;" id="medias-Posts-1"></iframe>', $result);
    }
    
    
}
