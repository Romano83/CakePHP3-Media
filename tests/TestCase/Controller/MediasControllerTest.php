<?php
namespace Media\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * Media\Controller\MediasController Test Case
 */
class MediasControllerTest extends IntegrationTestCase {

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.media.medias',
        'plugin.media.posts'
    ];
    
    /* (non-PHPdoc)
     * @see \Cake\TestSuite\TestCase::setUp()
     */
    public function setUp() {
        parent::setUp();
        $config = TableRegistry::exists('Medias') ? [] : ['className' => 'Media\Model\Table\MediasTable'];
        $this->Medias = TableRegistry::get('Medias', $config);
        $this->Posts = TableRegistry::get('Posts');
    }
    
    /* (non-PHPdoc)
     * @see \Cake\TestSuite\IntegrationTestCase::tearDown()
     */
    public function tearDown() {
        parent::tearDown();
        unset($this->Medias);
        unset($this->Posts);
        TableRegistry::clear();
    }


    /**
     * Test canUploadMedias method
     *
     * @return void
     */
    public function testCanUploadMedias() {
        
    }

    /**
     * Test beforeFilter method
     *
     * @return void
     */
    public function testBeforeFilter() {
        
    }

    /**
     * Test testIndexWithCanUploadMediasMethod
     *
     * @return void
     */
    public function testIndexWithCanUploadMediasMethod() {
        $this->get('/media/medias/index/Posts/1');
        
        $this->assertResponseOK();
    }
    
    /**
     * Test testIndexWithoutBehaviorLoaded
     *
     * @return void
     */
    public function testIndexWithoutBehaviorLoaded() {        
	    	$this->get('/media/medias/index/Posts/1');
	    		    	
	    	$this->assertResponseOk();
	    	$this->assertResponseContains('<h1>Error</h1>');
	    	$this->assertResponseContains("<p>Table {0}Table doesn't have 'Media' behavior</p>");
    }
    
    /**
     * Test testListingMedias
     *
     * @return void
     */
    public function testListingMedias() {     
        $this->Posts->addBehavior('Media.Media', [
            'path' => WWW_ROOT . 'img' . DS . 'upload' . DS . '%y' . DS . '%m' . DS. '%f',
            'extensions' => ['jpg', 'png', 'gif'],
            'limit' => 0,
            'maw_width' => 0,
            'maw_height' => 0,
            'size' => 0
        ]);
	    	$this->get('/media/medias/index/Posts/1');
	    		    	
	    	$this->assertResponseOk();
	    	$this->assertEquals(2, count($this->viewVariable('medias')));
	    	$this->assertEquals(1, count($this->viewVariable('thumbID')));
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit() {
        
    }

    /**
     * Test upload method
     *
     * @return void
     */
    public function testUpload() {    
        $this->Posts->addBehavior('Media.Media', [
            'path' => 'img' . DS . 'upload' . DS . '%y' . DS . '%m' . DS. '%f',
            'extensions' => ['jpg', 'png', 'gif'],
            'limit' => 0,
            'maw_width' => 0,
            'maw_height' => 0,
            'size' => 0
        ]);
	    	$this->Medias = $this->getMockForModel('Media.Medias', array('move_uploaded_file'));
        $this->Medias->expects($this->any())->method('move_uploaded_file')->will($this->returnCallback('test_move_uploaded_file'));
        $this->image = TMP . 'testHelper.png';
        $data = [
            'file' => [
                'name' => 'testHelper.png',
                'type' => 'image/png',
                'tmp_name' => $this->image,
                'error' => UPLOAD_ERR_OK,
                'size' => 52085
            ]
        ];
        $this->post('/media/medias/upload/Posts/1', $data);
        
        $media = $this->Medias->find()->where(['file LIKE' => '%testHelper.png%'])->first();
        $this->assertEquals(1, $media->ref_id);
        $this->assertEquals('Posts', $media->ref);
        $this->assertEquals(true, \file_exists(WWW_ROOT . trim($media->file, '/')));
        $this->Medias->delete($media);
    }
    
//     public function testUploadWithWrongExtension() {    
//         $this->Posts->addBehavior('Media.Media', [
//             'path' => WWW_ROOT . 'img' . DS . 'upload' . DS . '%y' . DS . '%m' . DS. '%f',
//             'extensions' => ['jpg', 'png', 'gif'],
//             'limit' => 0,
//             'maw_width' => 0,
//             'maw_height' => 0,
//             'size' => 0
//         ]);
//         $this->Medias = $this->getMockForModel('Media.Medias', array('move_uploaded_file'));
//         $this->Medias->expects($this->any())->method('move_uploaded_file')->will($this->returnCallback('test_move_uploaded_file'));
//         $this->image = TMP . 'testHelper.png';
//         $data = [
//             'file' => [
//                 'name' => 'testHelper.pdf',
//                 'type' => 'image/png',
//                 'tmp_name' => $this->image,
//                 'error' => UPLOAD_ERR_OK,
//                 'size' => 52085
//             ]
//         ];
//         $this->post('/media/medias/upload/Posts/1', $data);
//         $this->assertResponseOk();
        
// //         $this->configRequest([
// //             'headers' => ['Accept' => 'application/json']
// //         ]);
// //         $expected = ['error' => [
// //            'file' => [
// //                'global' => "You don't have the permission to upload this filetype (jpg, png, gif only)"
// //            ] 
// //         ]];
// //         $expected = \json_encode($expected, JSON_PRETTY_PRINT);
// //         $this->assertEquals($expected, $this->_response->body());
//     }

    /**
     * Test update method
     *
     * @return void
     */
    public function testUpdate() {
        
    }

    /**
     * Test testDeleteWithoutAjaxRequest
     *
     * @return void
     */
    public function testDeleteWithoutAjaxRequest() {
        $this->delete('/media/medias/delete/1');
        $this->getExpectedException('BadRequestException');
        $this->assertResponseError();
    }

    /**
     * Test testDeleteWithoutGoodId
     *
     * @return void
     */
    public function testDeleteWithoutGoodId() {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->delete('/media/medias/delete/5');
        $this->getExpectedException('NotFoundException');
        $this->assertResponseCode(404);
    }
    /**
     * Test testDeleteWithoutGoodId
     *
     * @return void
     */
    public function testDeleteWithGoodId() {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->delete('/media/medias/delete/1');
        $this->assertResponseOk();
    }

    /**
     * Test testThumbWithNotFoundMedia
     *
     * @return void
     */
    public function testThumbWithNotFoundMedia() {
        $this->get('/media/medias/thumb/5');
        $this->getExpectedException('NotFoundException');
        $this->assertResponseCode(404);
    }

    /**
     * Test testThumbWithExistingMedia
     *
     * @return void
     */
    public function testThumbWithExistingMedia() {
        $this->get('media/medias/thumb/1');
        $post = $this->Posts->get(1);
        $this->assertEquals(1, $post->media_id);
        $this->assertRedirectContains('/media/medias/index/Posts/1');
    }

    /**
     * Test testOrderWithoutAjaxRequest
     *
     * @return void
     */
    public function testOrderWithoutAjaxRequest() {
        $this->get('/media/medias/order');
        $this->getExpectedException('BadRequestException');
        $this->assertResponseError();
    }

    /**
     * Test testOrderWithoutAjaxRequest
     *
     * @return void
     */
    public function testOrder() {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data = [
          'Media' => [1 => 0, 2 => 1, 3 => 2]  
        ];
        $this->post('/media/medias/order', $data);
        $media = $this->Medias->find('list', ['keyField' => 'id', 'valueField' => 'position'])->toArray();
        $this->assertEquals(0, $media[1]);
        $this->assertEquals(1, $media[2]);
    }
}

