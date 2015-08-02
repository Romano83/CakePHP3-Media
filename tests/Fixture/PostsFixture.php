<?php
namespace Media\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class PostsFixture extends TestFixture
{

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
        'name' => [
            'type' => 'string',
            'length' => 255,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],
        'content' => [
            'type' => 'text',
            'length' => null,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],
        'online' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => '0',
            'comment' => ''
        ],
        'created' => [
            'type' => 'datetime'
        ],
        'updated' => [
            'type' => 'datetime'
        ],
        'media_id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => '0',
            'comment' => ''
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

    public $records = [
        [
            'id' => 1,
            'name' => 'First Article',
            'content' => 'First Article content',
            'online' => 1,
            'created' => '2007-03-18 10:39:23',
            'updated' => '2007-03-18 10:41:31',
            'media_id' => 1
        ],
        [
            'id' => 2,
            'name' => 'Second Article',
            'content' => 'Second Article content',
            'online' => 1,
            'created' => '2007-03-18 10:41:23',
            'updated' => '2007-03-18 10:43:31',
            'media_id' => 0
        ],
        [
            'id' => 3,
            'name' => 'Third Article',
            'content' => 'Third Article content',
            'online' => 1,
            'created' => '2007-03-18 10:43:23',
            'updated' => '2007-03-18 10:45:31',
            'media_id' => 0
        ]
    ];
}