<?php
namespace Media\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;

class MediaBehavior extends Behavior
{

    /**
     * Default options
     *
     * @var array
     */
    protected $config = [
        'path' => 'img/upload/%y/%m/%f',        // default upload path relative to webroot folder (see below for path parameters)
        'extensions' => ['jpg', 'png'],         // array of authorized extensions (lowercase)
        'limit' => 0,                           // limit number of upload file. Default: 0 (no limit)
        'max_width' => 0,                       // maximum authorized width for uploaded pictures. Default: 0 (no limitation) 
        'max_height' => 0,                      // maximum authorized height for uploaded pictures. Default: 0 (no limitation)
        'size' => 0,                            // maximum authorized size for uploaded pictures (in kb). Default: 0 (no limitation)
        'resize' => [                           // Array of options to resize images or false
            'sizes' => [
                'small'  => [
                    'width'  => '150',
                    'height' => '150',
                    'crop'   => true
                ],
                'medium' => [
                    'width'  => '350',
                    'height' => '350',
                    'crop'   => true
                ],
                'large'  => [
                    'width'  => '1024',
                    'height' => '1024',
                    'crop'   => false
                ],
            ],
            'quality' => 100                   // image quality after resize/crop (for .jpg images)
        ]
    ];
    
    /**
     * Add HasMany association in table whith this behavior.
     * If database table has 'media_id' field, the behavior add belongsTo association
     *
     * @see \Cake\ORM\Behavior::initialize()
     * @param array $config
     *            The configuration settings provided to this behavior.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->_table->medias = array_merge($this->config, $config);
        $this->_table->hasMany('Media', [
            'className' => 'Media.Medias',
            'foreignKey' => 'ref_id',
            'order' => 'Media.position ASC',
            'conditions' => 'ref = "' . $this->_table->getRegistryAlias() . '"',
            'dependant' => true
        ]);
        if ($this->_table->hasField('media_id')) {
            $this->_table->belongsTo('Thumb', [
                'className' => 'Media.Medias',
                'foreignKey' => 'media_id'
            ]);
        }
    }

}
