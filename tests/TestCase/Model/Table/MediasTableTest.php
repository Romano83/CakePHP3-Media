<?php
namespace Media\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Media\Model\Table\MediasTable;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Media\Test\Lib\Utility;

/**
 * Media\Model\Table\MediasTable Test Case
 */
class MediasTableTest extends TestCase
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

    /**
     * setUp method
     *
     * @return void
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
        $this->Utility = new Utility();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Medias);
        TableRegistry::clear();
        parent::tearDown();
    }

    /**
     * Test testBeforeDeleteWithOriginalFile method
     *
     * @covers Media\Model\Table\MediasTable::beforeDelete
     *
     * @return void
     */
    public function testBeforeDeleteWithOriginalFile()
    {
        copy($this->image, WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper.png');
        $this->assertEquals(true, file_exists(WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper.png'));
        $media = $this->Medias->get(1);
        $media = $this->Medias->delete($media);
        $this->assertEquals(false, file_exists(WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper.png'));
    }

    /**
     * Test testBeforeDeleteWithResizedFile method
     *
     * @covers Media\Model\Table\MediasTable::beforeDelete
     *
     * @return void
     */
    public function testBeforeDeleteWithResizedFile()
    {
        copy($this->resizedImage, WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper_50x50.jpg');
        $this->assertEquals(true, file_exists(WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper_50x50.jpg'));
        $media = $this->Medias->get(2);
        $this->Medias->delete($media);
        $this->assertEquals(false, file_exists(WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper_50x50.jpg'));
    }

    /**
     * Test testbeforeSave method
     *
     * @return void
     */
    public function testbeforeSave()
    {
        $file = [
            'file' => [
                'name' => 'testHelper.png',
                'type' => 'image/png',
                'tmp_name' => $this->image,
                'error' => UPLOAD_ERR_OK,
                'size' => 52085
            ]
        ];
        $data = [
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
        $table = TableRegistry::get($data['ref']);
        $table->addBehavior('Media.Media', [
            'path' => 'img' . DS . 'upload' . DS . '%y' . DS . '%m' . DS . '%f',
            'extensions' => [
                'jpg',
                'png',
                'gif'
            ],
            'limit' => 0,
            'maw_width' => 0,
            'maw_height' => 0,
            'size' => 0
        ]);
        
        $expected = [
            'id' => 5,
            'ref' => 'Posts',
            'ref_id' => 1,
            'file' => DS . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testhelper.png',
            'name' => null,
            'position' => 0,
            'caption' => null
        ];
        $media = $this->Medias->newEntity();
        $media = $this->Medias->patchEntity($media, $data, [
            'validation' => 'default'
        ]);
        
        $this->test_move_uploaded_file($data['file']['file']['tmp_name'], WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . $data['file']['file']['name']);
        $this->assertTrue(\file_exists(WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . $file['file']['name']));
        \unlink(WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . $file['file']['name']);
        
        $result = $this->Medias->save($media, $file);
        $this->assertInstanceOf('Media\Model\Entity\Media', $result);
        
        $media = $this->Medias->find()
            ->where([
            'id' => $result->id
        ])
            ->first()
            ->toArray();
        $this->assertEquals($expected, $media);
        
        $this->Medias->delete($result);
    }

    /**
     * Mock to test move_uploaded_file method
     *
     * @param string $filename            
     * @param string $destination            
     */
    public function test_move_uploaded_file($filename, $destination)
    {
        // debug(copy($filename, $destination));
        return copy($filename, $destination);
    }

    /**
     * Test testTestDuplicate method
     *
     * @covers Media\Model\Table\MediasTable::testDuplicate
     *
     * @return void
     */
    // public function testTestDuplicate()
    // {
    // $data = [
    // 'ref' => 'Posts',
    // 'ref_id' => 1,
    // 'file' => [
    // 'file' => [
    // 'name' => 'testHelper.png',
    // 'type' => 'image/png',
    // 'tmp_name' => $this->image,
    // 'error' => UPLOAD_ERR_OK,
    // 'size' => 52085
    // ]
    // ]
    // ];
    // $ref = TableRegistry::get($data['ref']);
    // $ref->addBehavior('Media.Media');
    // $entity = new Entity($data);
    // $entity->isNew(true);
    // $media = $this->Medias->save($entity);
    // $id = $media->id;
    // $media = $this->Medias->get($id);
    // \var_dump($media);
    // $this->assertEquals('testHelper.png', basename($media->file));
    
    // $entity = new Entity($data);
    // $entity->isNew(true);
    // $media2 = $this->Medias->save($entity);
    // $id = $media2->id;
    // $media2 = $this->Medias->get($id);
    // $media2->file = WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '07' . DS . 'testHelper-1.png';
    // $this->assertEquals('testHelper-1.png', basename($media2->file));
    
    // $this->Medias->delete($media);
    // $this->Medias->delete($media2);
    // }
    
    /**
     * Test testValidationDefaultWithForbiddenExtension method
     *
     * @return void
     */
    public function testValidationDefaultWithForbiddenExtension()
    {
        $file = [
            'name' => 'testHelper.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $this->image,
            'error' => UPLOAD_ERR_OK,
            'size' => 52085
        ];
        $data = [
            'ref' => 'Posts',
            'ref_id' => 1,
            'file' => WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '07' . DS . $file['name']
        ];
        $table = TableRegistry::get($data['ref']);
        $table->addBehavior('Media.Media', [
            'path' => 'img' . DS . 'upload' . DS . '%y' . DS . '%m' . DS . '%f',
            'extensions' => [
                'jpg',
                'png',
                'gif'
            ]
        ]);
        $expected = [
            'file' => [
                'global'
            ]
        ];
        $media = $this->Medias->newEntity($data, [
            'validate' => 'default'
        ]);
        $result = $this->Medias->save($media);
        $this->assertFalse($result);
        $this->assertEquals($expected, $this->Utility->getL2keys($media->errors()));
    }

    /**
     * Test testValidationDefaultWithUploadLimit method
     *
     * @return void
     */
    public function testValidationDefaultWithLimit()
    {
        $file = [
            'name' => 'testHelper.png',
            'type' => 'image/png',
            'tmp_name' => $this->image,
            'error' => UPLOAD_ERR_OK,
            'size' => 52085
        ];
        $data = [
            'ref' => 'Posts',
            'ref_id' => 1,
            'file' => WWW_ROOT . 'img' . DS . 'upload' . DS . '2015' . DS . '07' . DS . $file['name']
        ];
        $table = TableRegistry::get($data['ref']);
        $table->addBehavior('Media.Media', [
            'path' => 'img' . DS . 'upload' . DS . '%y' . DS . '%m' . DS . '%f',
            'extensions' => [
                'jpg',
                'png',
                'gif'
            ],
            'limit' => 1
        ]);
        $expected = [
            'file' => [
                'global'
            ]
        ];
        $media = $this->Medias->newEntity($data, [
            'validate' => 'default'
        ]);
        $result = $this->Medias->save($media);
        $this->assertFalse($result);
        
        $media = $this->Medias->newEntity($data, [
            'validate' => 'default'
        ]);
        $result = $this->Medias->save($media);
        $this->assertFalse($result);
        $this->assertEquals($expected, $this->Utility->getL2keys($media->errors()));
    }

    /**
     * Test testValidationDefaultWithWidthLimit method
     *
     * @return void
     */
    public function testValidationDefaultWithWidthLimit()
    {
        $file = [
            'name' => 'testHelper.png',
            'type' => 'image/png',
            'tmp_name' => $this->image,
            'error' => UPLOAD_ERR_OK,
            'size' => 52085
        ];
        $data = [
            'ref' => 'Posts',
            'ref_id' => 1,
            'file' => [
                'file' => $file
            ]
        ];
        $table = TableRegistry::get($data['ref']);
        $table->addBehavior('Media.Media', [
            'path' => 'img' . DS . 'upload' . DS . '%y' . DS . '%m' . DS . '%f',
            'extensions' => [
                'jpg',
                'png',
                'gif'
            ],
            'max_width' => 150
        ]);
        $expected = [
            'file' => [
                'global'
            ]
        ];
        $media = $this->Medias->newEntity();
        $media = $this->Medias->patchEntity($media, $data, [
            'validation' => 'default'
        ]);
        $result = $this->Medias->save($media, $file);
        $this->assertFalse($result);
        $this->assertEquals($expected, $this->Utility->getL2keys($media->errors()));
    }

    /**
     * Test testValidationDefaultWithHeightLimit method
     *
     * @return void
     */
    public function testValidationDefaultWithHeightLimit()
    {
        $file = [
            'name' => 'testHelper.png',
            'type' => 'image/png',
            'tmp_name' => $this->image,
            'error' => UPLOAD_ERR_OK,
            'size' => 52085
        ];
        $data = [
            'ref' => 'Posts',
            'ref_id' => 1,
            'file' => [
                'file' => $file
            ]
        ];
        $table = TableRegistry::get($data['ref']);
        $table->addBehavior('Media.Media', [
            'path' => 'img' . DS . 'upload' . DS . '%y' . DS . '%m' . DS . '%f',
            'extensions' => [
                'jpg',
                'png',
                'gif'
            ],
            'max_height' => 150
        ]);
        $expected = [
            'file' => [
                'global'
            ]
        ];
        $media = $this->Medias->newEntity();
        $media = $this->Medias->patchEntity($media, $data, [
            'validation' => 'default'
        ]);
        $result = $this->Medias->save($media, $file);
        $this->assertFalse($result);
        $this->assertEquals($expected, $this->Utility->getL2keys($media->errors()));
    }

    /**
     * Test testValidationDefaultWithSizeLimit method
     *
     * @return void
     */
    public function testValidationDefaultWithSizeLimit()
    {
        $file = [
            'name' => 'testHelper.png',
            'type' => 'image/png',
            'tmp_name' => $this->image,
            'error' => UPLOAD_ERR_OK,
            'size' => 52085
        ];
        $data = [
            'ref' => 'Posts',
            'ref_id' => 1,
            'file' => [
                'file' => $file
            ]
        ];
        $table = TableRegistry::get($data['ref']);
        $table->addBehavior('Media.Media', [
            'path' => 'img' . DS . 'upload' . DS . '%y' . DS . '%m' . DS . '%f',
            'extensions' => [
                'jpg',
                'png',
                'gif'
            ],
            'size' => 50
        ]);
        $expected = [
            'file' => [
                'global'
            ]
        ];
        $media = $this->Medias->newEntity();
        $media = $this->Medias->patchEntity($media, $data, [
            'validation' => 'default'
        ]);
        $result = $this->Medias->save($media, $file);
        $this->assertFalse($result);
        $this->assertEquals($expected, $this->Utility->getL2keys($media->errors()));
    }
}

