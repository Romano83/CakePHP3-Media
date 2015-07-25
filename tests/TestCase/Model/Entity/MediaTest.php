<?php
namespace Media\Test\TestCase\Model\Entity;

use Cake\TestSuite\TestCase;
use Media\Model\Entity\Media;
use Cake\ORM\TableRegistry;

/**
 * Media\Model\Entity\Medias Test Case
 */
class MediaTest extends TestCase
{

		public $fixtures = ['plugin.media.medias'];
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Medias') ? [] : ['className' => 'Media\Model\Table\MediasTable'];
        $this->Medias = TableRegistry::get('Medias', $config);
	    	$this->pictures = ['jpg', 'png', 'gif', 'bmp'];
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Medias);

        parent::tearDown();
    }

    /**
     * Test testGetFileType method
     *
     * @return void
     */
    public function testGetFileType()
    {
    	$data = [
    		'ref' => 'Posts',
        'ref_id' => 1,
        'file' => WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '07' . DS . 'testHelper.png',
    	];
    	$media = $this->Medias->newEntity($data, ['validate' => false]);
    	$this->assertEquals('pic', $media->file_type);
    	
    	$data = [
    		'ref' => 'Posts',
        'ref_id' => 1,
        'file' => WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '07' . DS . 'document.pdf',
    	];
    	$media = $this->Medias->newEntity($data, ['validate' => false]);
    	$this->assertEquals('pdf', $media->file_type);
    }
    
    public function testGetFileIcon()
    {
    	$data = [
    		'ref' => 'Posts',
        'ref_id' => 1,
        'file' => WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '07' . DS . 'testHelper.png',
    	];
    	$media = $this->Medias->newEntity($data, ['validate' => false]);
    	$this->assertEquals($media->file, $media->file_icon);
    	
    	$data = [
    			'ref' => 'Posts',
    			'ref_id' => 1,
    			'file' => WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '07' . DS . 'document.pdf',
    	];
    	$media = $this->Medias->newEntity($data, ['validate' => false]);
    	$this->assertEquals('Media.pdf.png', $media->file_icon);
    }

   
}
