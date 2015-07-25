<?php
namespace Media\Model\Entity;

use Cake\ORM\Entity;

/**
 * Media Entity.
 */
class Media extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];

    private $pictures = [
        'jpg',
        'png',
        'gif',
        'bmp'
    ];

    public $icon;

    public $type;

    /**
     * @return string
     */
    protected function _getFileType()
    {
        if (isset($this->file)) {
            $extension = \pathinfo($this->file, PATHINFO_EXTENSION);
            if (! \in_array($extension, $this->pictures)) {
                return $this->type = $extension;
            } else {
                return $this->type = 'pic';
            }
        }
    }

    /**
     * @return string
     */
    protected function _getFileIcon()
    {
        if (isset($this->file)) {
            $extension = \pathinfo($this->file, PATHINFO_EXTENSION);
            if (! \in_array($extension, $this->pictures)) {
                return $this->icon = 'Media.' . $extension . '.png';
            } else {
                return $this->icon = $this->file;
            }
        }
    }
}
