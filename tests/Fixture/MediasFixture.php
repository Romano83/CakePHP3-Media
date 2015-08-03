<?php
namespace Media\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MediasFixture
 */
class MediasFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => '',
            'autoIncrement' => true,
            'precision' => null
        ],
        'ref' => [
            'type' => 'string',
            'length' => 60,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'ref_id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        'file' => [
            'type' => 'string',
            'length' => 255,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'name' => [
            'type' => 'string',
            'length' => 255,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'position' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => 0,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],
        'caption' => [
            'type' => 'text',
            'length' => null,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],
        '_indexes' => [
            'ref_id' => [
                'type' => 'index',
                'columns' => [
                    'ref_id'
                ],
                'length' => []
            ],
            'ref' => [
                'type' => 'index',
                'columns' => [
                    'ref'
                ],
                'length' => []
            ]
        ],
        '_constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => [
                    'id'
                ],
                'length' => []
            ]
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ]
    ];
    // @codingStandardsIgnoreEnd
    
    /**
     * Records
     *
     * @var array
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'ref' => 'Posts',
                'ref_id' => 1,
                'file' => 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper.png',
                'name' => 'Lorem ipsum dolor sit amet',
                'position' => 0,
                'caption' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.'
            ],
            [
                'id' => 2,
                'ref' => 'Posts',
                'ref_id' => 1,
                'file' => 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper_50x50.jpg',
                'name' => 'Lorem ipsum dolor sit amet',
                'position' => 0,
                'caption' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.'
            ],
            [
                'id' => 3,
                'ref' => 'Posts',
                'ref_id' => 2,
                'file' => 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'document.pdf',
                'name' => 'Lorem ipsum dolor sit amet',
                'position' => 1,
                'caption' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.'
            ],
            [
                'id' => 4,
                'ref' => 'Pages',
                'ref_id' => 2,
                'file' => 'img' . DS . 'upload' . DS . '2015' . DS . '08' . DS . 'testHelper.png',
                'name' => 'Lorem ipsum dolor sit amet',
                'position' => 1,
                'caption' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.'
            ]
        ];
        parent::init();
    }
}
