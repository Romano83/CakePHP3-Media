<?php
namespace Media\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Media\Controller\MediasController;
use Cake\ORM\TableRegistry;

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
    
    /* (non-PHPdoc)
     * @see \Cake\TestSuite\TestCase::setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Medias') ? [] : ['className' => 'Media\Model\Table\MediasTable'];
        $this->Medias = TableRegistry::get('Medias', $config);   
    }
    
    /* (non-PHPdoc)
     * @see \Cake\TestSuite\IntegrationTestCase::tearDown()
     */
    public function tearDown()
    {
        parent::tearDown();
    }


    /**
     * Test canUploadMedias method
     *
     * @return void
     */
    public function testCanUploadMedias()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test beforeFilter method
     *
     * @return void
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get(['controller' => 'medias', 'action' => 'index', 'plugin' => 'media', 'Posts', 1]);
        
        $this->assertResponseOk();
        $this->assertResponseContains('testHelper.png');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test upload method
     *
     * @return void
     */
    public function testUpload()
    {
        
    }

    /**
     * Test update method
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test thumb method
     *
     * @return void
     */
    public function testThumb()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test order method
     *
     * @return void
     */
    public function testOrder()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
