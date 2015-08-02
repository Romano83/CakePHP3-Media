<?php
namespace Media\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * Media\Controller\MediasController Test Case
 */
class MediasControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.media.medias',
        'plugin.media.posts'
    ];

    /*
     * (non-PHPdoc)
     * @see \Cake\TestSuite\TestCase::setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->image = TMP . 'testHelper.png';
        $this->resizedImage = TMP . 'testHelper_50x50.jpg';
        $config = TableRegistry::exists('Medias') ? [] : [
            'className' => 'Media\Model\Table\MediasTable'
        ];
        $this->Medias = TableRegistry::get('Medias', $config);
        $this->Medias = $this->getMockForModel('Media.Medias', array(
            'move_uploaded_file'
        ));
        $this->Medias->expects($this->any())
            ->method('move_uploaded_file')
            ->will($this->returnCallback([
            $this,
            'test_move_uploaded_file'
        ]));
        $this->Posts = TableRegistry::get('Posts');
    }

    /*
     * (non-PHPdoc)
     * @see \Cake\TestSuite\IntegrationTestCase::tearDown()
     */
    public function tearDown()
    {
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
    public function testCanUploadMedias()
    {}

    /**
     * Test beforeFilter method
     *
     * @return void
     */
    public function testBeforeFilter()
    {}

    /**
     * Test testIndexWithoutCanUploadMediasMethod
     *
     * @return void
     */
    public function testIndexWithoutCanUploadMediasMethod()
    {
        $this->get('/media/medias/index/Posts/2');
        $this->assertResponseError();
        $this->assertResponseCode(403);
    }

    /**
     * Test testIndexWithCanUploadMediasMethod
     *
     * @return void
     */
    public function testIndexWithCanUploadMediasMethod()
    {
        $this->get('/media/medias/index/Posts/1');
        
        $this->assertResponseOK();
    }

    /**
     * Test testIndexWithoutBehaviorLoaded
     *
     * @return void
     */
    public function testIndexWithoutBehaviorLoaded()
    {
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
    public function testListingMedias()
    {
        copy($this->image, WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper.png');
        copy($this->resizedImage, WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper_50x50.jpg');
        $this->Posts->addBehavior('Media.Media');
        $this->get('/media/medias/index/Posts/1');
        
        $this->assertResponseOk();
        $this->assertEquals(2, count($this->viewVariable('medias')));
        $this->assertEquals(1, count($this->viewVariable('thumbID')));
        unlink(WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper.png');
        unlink(WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper_50x50.jpg');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->get('media/medias/edit/?editor=tinymce&id=content&media_id=1&alt=&class=');
        $this->assertResponseOk();
        $expected = [
            'src' => 'img/upload/2015/08/testHelper.png',
            'editor' => 'tinymce',
            'ref' => 'Posts',
            'ref_id' => 1,
            'alt' => '',
            'class' => '',
            'caption' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'type' => 'pic',
            'id' => 'content',
            'media_id' => '1'
        ];
        $this->assertEquals($expected, $this->viewVariable('data'));
    }

    /**
     * Test testEditWithWrongId
     *
     * @return void
     */
    public function testEditWithWrongId()
    {
        $this->get('media/medias/edit/?editor=tinymce&id=content&media_id=5&alt=&class=');
        
        $this->assertResponseError();
        $this->assertResponseCode(404);
    }

    /**
     * Test testEditWithoutPermission
     *
     * @return void
     */
    public function testEditWithoutPermission()
    {
        $this->get('media/medias/edit/?editor=tinymce&id=content&media_id=4&alt=&class=');
        
        $this->assertResponseError();
        $this->assertResponseCode(403);
    }

    /**
     * test testUploadWithoutPermission
     *
     * @return void
     */
    public function testUploadWithoutPermission()
    {
        $data = [
            'file' => [
                'name' => 'testHelper.png',
                'type' => 'image/png',
                'tmp_name' => $this->image,
                'error' => UPLOAD_ERR_OK,
                'size' => 52085
            ]
        ];
        $this->post('/media/medias/upload/Pages/2', $data);
        
        $this->assertResponseError();
        $this->assertResponseCode(403);
    }

    /**
     * Test upload method
     *
     * @return void
     */
    public function testUpload()
    {
        $this->Posts->addBehavior('Media.Media');
        $this->Medias = $this->getMockForModel('Media.Medias', array(
            'move_uploaded_file'
        ));
        $this->Medias->expects($this->once())
            ->method('move_uploaded_file')
            ->will($this->returnCallback([
            $this,
            'test_move_uploaded_file'
        ]));
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
        
        $media = $this->Medias->find()
            ->where([
            'file LIKE' => '%testHelper.png%'
        ])
            ->first();
        $this->assertEquals(1, $media->ref_id);
        $this->assertEquals('Posts', $media->ref);
        $this->assertEquals(true, \file_exists(WWW_ROOT . trim($media->file, '/')));
        $this->Medias->delete($media);
    }
    
    // public function testUploadWithWrongExtension() {
    // $this->Posts->addBehavior('Media.Media', [
    // 'path' => 'img' . DS . 'upload' . DS . '%y' . DS . '%m' . DS. '%f',
    // 'extensions' => ['jpg', 'png', 'gif'],
    // 'limit' => 0,
    // 'maw_width' => 0,
    // 'maw_height' => 0,
    // 'size' => 0
    // ]);
    // $this->Medias = $this->getMockForModel('Media.Medias', array('move_uploaded_file'));
    // $this->Medias->expects($this->any())->method('move_uploaded_file')->will($this->returnCallback('test_move_uploaded_file'));
    // $this->image = TMP . 'testHelper.png';
    // $data = [
    // 'file' => [
    // 'name' => 'testHelper.pdf',
    // 'type' => 'image/png',
    // 'tmp_name' => $this->image,
    // 'error' => UPLOAD_ERR_OK,
    // 'size' => 52085
    // ]
    // ];
    // $this->post('/media/medias/upload/Posts/1', $data);
    // $this->assertResponseOk();
    
    // // $this->configRequest([
    // // 'headers' => ['Accept' => 'application/json']
    // // ]);
    // // $expected = ['error' => [
    // // 'file' => [
    // // 'global' => "You don't have the permission to upload this filetype (jpg, png, gif only)"
    // // ]
    // // ]];
    // // $expected = \json_encode($expected, JSON_PRETTY_PRINT);
    // // $this->assertEquals($expected, $this->_response->body());
    // }
    
    /**
     * Test testUpdateWithoutAjaxRequest
     *
     * @return void
     */
    public function testUpdateWithoutAjaxRequest()
    {
        $this->get('/media/medias/update/1');
        $this->assertResponseError();
        $this->assertResponseCode(400);
    }

    /**
     * Test testUpdateWithoutImplementedBehavior
     *
     * @return void
     */
    public function testUpdateWithoutImplementedBehavior()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data = [
            'name' => 'New title',
            'caption' => ''
        ];
        $this->put('/media/medias/update/1', $data);
        $this->assertResponseFailure();
        $this->assertResponseCode(501);
    }

    /**
     * Test testUpdateWithoutGoodId
     *
     * @return void
     */
    public function testUpdateWithoutGoodId()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->Posts->addBehavior('Media.Media');
        $data = [
            'name' => 'New title',
            'caption' => ''
        ];
        $this->put('/media/medias/update/5', $data);
        $this->assertResponseError();
        $this->assertResponseCode(404);
    }

    /**
     * Test testUpdateWithoutPermission
     *
     * @return void
     */
    public function testUpdateWithoutPermission()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->Posts->addBehavior('Media.Media');
        $data = [
            'name' => 'New title',
            'caption' => ''
        ];
        $this->put('/media/medias/update/4', $data);
        $this->assertResponseError();
        $this->assertResponseCode(403);
    }

    /**
     * Test testUpdateWithName
     *
     * @return void
     */
    public function testUpdateWithName()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->Posts->addBehavior('Media.Media');
        $data = [
            'name' => 'New title',
            'caption' => ''
        ];
        $this->put('/media/medias/update/1', $data);
        $this->assertResponseOk();
        $media = $this->Medias->get(1);
        $this->assertEquals('New title', $media->name);
    }

    /**
     * Test testUpdateWithCaption
     *
     * @return void
     */
    public function testUpdateWithCaption()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->Posts->addBehavior('Media.Media');
        $data = [
            'name' => '',
            'caption' => 'New caption'
        ];
        $this->put('/media/medias/update/1', $data);
        $this->assertResponseOk();
        $media = $this->Medias->get(1);
        $this->assertEquals('New caption', $media->caption);
    }

    /**
     * Test testUpdateWithNameAndCaption
     *
     * @return void
     */
    public function testUpdateWithNameAndCaption()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->Posts->addBehavior('Media.Media');
        $data = [
            'name' => 'New title',
            'caption' => 'New caption'
        ];
        $this->put('/media/medias/update/1', $data);
        $this->assertResponseOk();
        $media = $this->Medias->get(1);
        $this->assertEquals('New title', $media->name);
        $this->assertEquals('New caption', $media->caption);
    }

    /**
     * Test testDeleteWithoutAjaxRequest
     *
     * @return void
     */
    public function testDeleteWithoutAjaxRequest()
    {
        $this->delete('/media/medias/delete/1');
        $this->assertResponseError();
        $this->assertResponseCode(400);
    }

    /**
     * Test testDeleteWithoutGoodId
     *
     * @return void
     */
    public function testDeleteWithoutGoodId()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->delete('/media/medias/delete/5');
        $this->assertResponseCode(404);
    }

    /**
     * Test testDeleteWithoutPermission
     *
     * @return void
     */
    public function testDeleteWithoutPermission()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->delete('/media/medias/delete/4');
        $this->assertResponseError();
        $this->assertResponseCode(403);
    }

    /**
     * Test testDeleteWithoutGoodId
     *
     * @return void
     */
    public function testDeleteWithGoodId()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->delete('/media/medias/delete/1');
        $this->assertResponseOk();
    }

    /**
     * Test testThumbWithNotFoundMedia
     *
     * @return void
     */
    public function testThumbWithNotFoundMedia()
    {
        $this->get('/media/medias/thumb/5');
        $this->assertResponseError();
        $this->assertResponseCode(404);
    }

    /**
     * Test testThumbWithoutPermission
     *
     * @return void
     */
    public function testThumbWithoutPermission()
    {
        $this->get('/media/medias/thumb/4');
        $this->assertResponseError();
        $this->assertResponseCode(403);
    }

    /**
     * Test testThumbWithExistingMedia
     *
     * @return void
     */
    public function testThumbWithExistingMedia()
    {
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
    public function testOrderWithoutAjaxRequest()
    {
        $this->get('/media/medias/order');
        $this->getExpectedException('BadRequestException');
        $this->assertResponseError();
        $this->assertResponseCode(400);
    }

    /**
     * Test testOrderWithoutAjaxRequest
     *
     * @return void
     */
    public function testOrder()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data = [
            'Media' => [
                1 => 0,
                2 => 1,
                3 => 2
            ]
        ];
        $this->post('/media/medias/order', $data);
        $media = $this->Medias->find('list', [
            'keyField' => 'id',
            'valueField' => 'position'
        ])->toArray();
        // $this->assertEquals(0, $media[1]);
        // $this->assertEquals(1, $media[2]);
        // $this->assertEquals(2, $media[3]);
    }
}

