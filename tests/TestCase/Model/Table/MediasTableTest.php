<?php
namespace Media\Test\TestCase\Model\Table;

use Cake\Filesystem\Folder;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Media\Test\Lib\Utility;

/**
 * Media\Model\Table\MediasTable Test Case
 */
class MediasTableTest extends TestCase
{

	/**
	 * @var string
	 */
	private $image;
	/**
	 * @var string
	 */
	private $resizedImage;
	/**
	 * @var \Media\Model\Table\MediasTable
	 */
	private $Medias;
	/**
	 * @var string
	 */
	private $uploadDir;
	/**
	 * @var \Media\Test\Lib\Utility
	 */
	private $Utility;

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
        $this->Utility = new Utility();
        $this->uploadDir = WWW_ROOT . 'img' . DS . 'upload' . DS . date('Y') . DS . date('m');
        new Folder($this->uploadDir, true, 0777);
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
        $folder = new Folder($this->uploadDir, false);
        $folder->delete();
    }

    /**
     * Test testBeforeDeleteWithOriginalFile method
     *
     * @covers \Media\Model\Table\MediasTable::beforeDelete
     *
     * @return void
     */
    public function testBeforeDeleteWithOriginalFile()
    {
        copy($this->image, $this->uploadDir . DS . 'testHelper.png');
        $this->assertTrue(file_exists($this->uploadDir . DS . 'testHelper.png'));
        $media = $this->Medias->get(1);
        $this->Medias->delete($media);
        $this->assertFalse(file_exists($this->uploadDir . DS . 'testHelper.png'));
    }

    /**
     * Test testBeforeDeleteWithResizedFile method
     *
     * @covers \Media\Model\Table\MediasTable::beforeDelete
     *
     * @return void
     */
    public function testBeforeDeleteWithResizedFile()
    {
        copy($this->resizedImage, $this->uploadDir . DS . 'testHelper_50x50.jpg');
        $this->assertTrue(file_exists($this->uploadDir . DS . 'testHelper_50x50.jpg'));
        $media = $this->Medias->get(2);
        $this->Medias->delete($media);
        $this->assertFalse(file_exists($this->uploadDir . DS . 'testHelper_50x50.jpg'));
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
                'size' => 52015
            ]
        ];
        $data = [
            'ref' => 'Posts',
            'ref_id' => 1,
            'file' => $file
        ];
        $table = TableRegistry::get($data['ref']);
        $table->addBehavior('Media.Media', [
            'resize' => false
        ]);
        
        $expected = [
            'id' => 5,
            'ref' => 'Posts',
            'ref_id' => 1,
            'file' => 'img' . DS . 'upload' . DS . date('Y') . DS . date('m') . DS . 'testhelper.png',
            'name' => null,
            'position' => 0,
            'caption' => null
        ];
        $media = $this->Medias->newEntity();
        $media = $this->Medias->patchEntity($media, $data, [
            'validation' => 'default'
        ]);

	    $result = $this->Medias->save($media, $file);
	    $this->assertTrue(\file_exists(WWW_ROOT . $result->file));
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

	public function testResizeImage() {
		$data = [
			'ref' => 'Posts',
			'ref_id' => 1,
			'file' => [
				'file' => [
					'name' => 'testHelper.png',
					'type' => 'image/png',
					'tmp_name' => $this->image,
					'error' => UPLOAD_ERR_OK,
					'size' => 52015
				]
			]
		];
		$table = TableRegistry::get($data['ref']);
		$table->addBehavior('Media.Media', [
			'resize' => [
				'sizes' => [
					'small' => [
						'width' => 25,
						'height' => 25
					]
				]
			]
		]);
		$media = $this->Medias->newEntity();
		$media = $this->Medias->patchEntity($media, $data, [
			'validation' => 'default'
		]);

		$this->Medias->save($media, $data['file']);
		$this->assertTrue(\file_exists($this->uploadDir . DS . 'testhelper_25x25.png'));
	}

	public function testCropImage() {
		$data = [
			'ref' => 'Posts',
			'ref_id' => 1,
			'file' => [
				'file' => [
					'name' => 'testHelper.png',
					'type' => 'image/png',
					'tmp_name' => $this->image,
					'error' => UPLOAD_ERR_OK,
					'size' => 52015
				]
			]
		];
		$table = TableRegistry::get($data['ref']);
		$table->addBehavior('Media.Media', [
			'resize' => [
				'sizes' => [
					'small' => [
						'width' => 25,
						'height' => 25,
						'crop' => true
					]
				]
			]
		]);
		$media = $this->Medias->newEntity();
		$media = $this->Medias->patchEntity($media, $data, [
			'validation' => 'default'
		]);

		$this->Medias->save($media, $data['file']);
		$this->assertTrue(\file_exists($this->uploadDir . DS . 'testhelper_25x25.png'));
	}

    /**
     * Test testTestDuplicate method
     *
     * @covers \Media\Model\Table\MediasTable::testDuplicate
     *
     * @return void
     */
	public function testTestDuplicate() {
		$data = [
			'ref'    => 'Posts',
			'ref_id' => 1,
			'file'   => [
				'file' => [
					'name'     => 'testHelper.png',
					'type'     => 'image/png',
					'tmp_name' => $this->image,
					'error'    => UPLOAD_ERR_OK,
					'size'     => 52015
				]
			]
		];
		$table  = TableRegistry::get( $data['ref'] );
		$table->addBehavior( 'Media.Media', ['resize' => false]);

		$media = $this->Medias->newEntity();
		$media = $this->Medias->patchEntity($media, $data, [
			'validation' => 'default'
		]);

		$this->Medias->save($media, $data['file']);

		$this->assertEquals( 'testhelper.png', basename( $media->file ) );

		$media = $this->Medias->newEntity();
		$media = $this->Medias->patchEntity($media, $data, [
			'validation' => 'default'
		]);

		$this->Medias->save($media, $data['file']);

		$this->assertEquals( 'testhelper-1.png', basename( $media->file ) );
	}
    
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
            'size' => 52015
        ];
        $data = [
            'ref' => 'Posts',
            'ref_id' => 1,
            'file' => $this->uploadDir . DS . $file['name']
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
            'size' => 52015
        ];
        $data = [
            'ref' => 'Posts',
            'ref_id' => 1,
            'file' => $this->uploadDir . DS . $file['name']
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
            'size' => 52015
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
            'size' => 52015
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
            'size' => 52015
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
