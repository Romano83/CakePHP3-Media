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
        'path' => 'img/uploads/%y/%m/%f',
        'extensions' => [
            'jpg',
            'png'
        ],
        'limit' => 0,
        'max_width' => 0,
        'max_height' => 0,
        'size' => 0
    ]; // in kB

    /**
     * Add HasMany association in table whith this behavior.
     * If database table has 'media_id' field, the behavior add belongsTo association
     * 
     * (non-PHPdoc)
     * @see \Cake\ORM\Behavior::initialize()
     * @param array $config The configuration settings provided to this behavior.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->_table->medias = array_merge($this->config, $config);
        $this->_table->hasMany('Media', [
            'className' => 'Media.Medias',
            'foreignKey' => 'ref_id',
            'order' => 'Media.position ASC',
            'conditions' => 'ref = "' . $this->_table->alias() . '"',
            'dependant' => true
        ]);
        if ($this->_table->hasField('media_id')) {
            $this->_table->belongsTo('Thumb', [
                'className' => 'Media.Medias',
                'foreignKey' => 'media_id'
            ]);
        }
    }

    /**
     * 
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Entity $entity
     * @param \ArrayObject $options
     * @return void
     */
    public function afterSave(Event $event, Entity $entity, \ArrayObject $options)
    {
        if (! empty($entity->thumb->name)) {
            $file = $entity->thumb;
            $mediaId = $entity->media_id;
            
            if ($mediaId != 0) {
                $entity->Media->delete($mediaId);
            }
            $entity->Media->save([
                'ref_id' => $entity->id,
                'ref' => $entity->name,
                'file' => $file
            ]);
            $entity->saveField('media_id', $entity->Media->id);
        }
    }
}
