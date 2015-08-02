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
        $this->image = TMP . 'testHelper.png';
        $config = TableRegistry::exists('Medias') ? [] : ['className' => 'Media\Model\Table\MediasTable'];
        $this->Model = TableRegistry::get('Medias', $config);
        $this->Posts = TableRegistry::get('Posts');
        $this->Posts->addAssociations([
            'hasMany' => [
                    'Media' => [
                        'className' => 'Media.Medias',
                        'foreignKey' => 'ref_id',
                        'order' => 'Media.position ASC',
                        'conditions' => 'ref = "' . $this->Model->alias() . '"',
                        'dependant' => true
                    ]
                ],
            'belongsTo' => [
                'Thumb' => [
                    'className' => 'Media.Medias',
                    'foreignKey' => 'media_id'
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
        $this->Posts->addBehavior('Media.Media');
        $file = [
            'file' => [
                'name' => 'testHelper.png',
                'type' => 'image/png',
                'tmp_name' => $this->image,
                'error' => UPLOAD_ERR_OK,
                'size' => 52085
            ]
        ];
        $mediaData = [
            'ref' => 'Posts',
        		'ref_id' => 1,
        		'file' => [
        			  	'file' => [
        				      'name' => 'testHelper.png',
        					    'type' => 'image/png',
        					    'tmp_name' => $this->image,
        					    'error' => UPLOAD_ERR_OK,
        					    'size' => 52085
        			    ]
        		]
        ];
        $media = $this->Model->newEntity();
        $media = $this->Model->patchEntity($media, $mediaData, ['validation' => 'default']);
        $media = $this->Model->save($media, $file);
        
        $postData = [
            'name' => 'Fourth Article',
            'content' => 'Fourth Article content',
            'online' => 1,
            'created' => '2007-03-18 10:39:23',
            'updated' => '2007-03-18 10:41:31',
            'media_id' => 0,
            'media' => [
                $media
            ]
        ];
        $post = $this->Posts->newEntity($postData);
        $post = $this->Posts->save($post);

//         $result = $this->Posts->get($post->id);
    }
}
