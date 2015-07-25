<?php
namespace Media\Test\TestCase\Model\Behavior;

use Cake\TestSuite\TestCase;
use Media\Model\Behavior\MediaBehavior;
use Cake\ORM\TableRegistry;

/**
 * Media\Model\Behavior\MediasBehavior Test Case
 */
class MediaBehaviorTest extends TestCase
{

    public $fixtures = ['plugin.media.medias', 'plugin.media.posts'];
    
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Medias') ? [] : ['className' => 'Media\Model\Table\MediasTable'];
        $this->Model = TableRegistry::get('Medias', $config);
        $this->Posts = TableRegistry::get('Posts');
        $this->Posts->addAssociations([
            'hasMany' => [
                    'Media' => [
                        'className' => 'Media.Medias'
                    ]
                ],
            'belongsTo' => [
                'Thumb' => [
                    'className' => 'Media.Medias'
                ]
            ]
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Model);
        TableRegistry::clear();
        parent::tearDown();
    }
    
    /**
     * test testInitialize method
     * 
     * @cover Media\Model\Behavior\MediaBehavior::initialize
     * @return void
     */
    public function testInitialize()
    {
         $this->assertTrue($this->Posts->associations()->has('media'));
         $this->assertTrue($this->Posts->associations()->has('thumb'));
    }

    /**
     * test afterSave method
     * 
     * @cover \Media\Model\Behavior\MediaBehavior::afterSave
     * @return void
     */
    public function testAfterSave()
    {
        
    }
}
