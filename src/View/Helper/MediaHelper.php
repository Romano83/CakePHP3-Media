<?php
namespace Media\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

class MediaHelper extends Helper
{

    /**
     * Default Helpers
     * 
     * @var array $helpers
     */
    public $helpers = [
        'Html',
        'Form',
        'Url'
    ];

    public $explorer = false;

    /**
     * Constructor
     * 
     * @param \Cake\View\View $View    The View this helper is being attached to.
     * @param array $config            Configuration settings for the helper. 
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
    }

    /**
     * 
     * @param string $fieldName
     * @param \Cake\ORM\Entity $ref
     * @param int $refId
     * @param array $options
     * @return string
     */
    public function tinymce($fieldName, $ref, $refId, array $options = [])
    {
        $this->Html->script('/media/js/tinymce/tinymce.min.js', [
            'block' => true
        ]);
        $this->Html->script('/media/js/tinymce/editor.js', [
            'block' => true
        ]);
        return $this->textarea($fieldName, $ref, $refId, 'tinymce', $options);
    }

    /**
     * 
     * @param string $fieldName
     * @param \Cake\ORM\Entity $ref
     * @param int $refId
     * @param array $options
     * @return string
     */
    public function ckeditor($fieldName, $ref, $refId, array $options = [])
    {
        $this->Html->script('/media/js/ckeditor/ckeditor.js', [
            'block' => true
        ]);
        return $this->textarea($fieldName, $ref, $refId, 'ckeditor', $options);
    }

    /**
     * 
     * @param string $fieldName
     * @param \Cake\ORM\Entity $ref
     * @param int $refId
     * @param bool|string $editor
     * @param array $options
     * @return string
     */
    public function textarea($fieldName, $ref, $refId, $editor = false, array $options = [])
    {
        $options = \array_merge([
            'label' => false,
            'style' => 'width:100%;height:500px',
            'rows' => 160,
            'type' => 'textarea',
            'class' => "wysiwyg $editor"
        ], $options);
        $html = $this->Form->input($fieldName, $options);
        if (isset($refId) && ! $this->explorer) {
            $html .= '<input type="hidden" id="explorer" value="' . $this->Url->build('/media/medias/index/' . $ref . '/' . $refId) . '">';
            $html .= '<input type="hidden" id="edit" value="' . $this->Url->build('/media/medias/edit/') . '">';
            $this->explorer = true;
        }
        return $html;
    }

    /**
     * @param Entity $ref
     * @param int $refId
     * @return string
     */
    public function iframe($ref, $refId)
    {
        return '<iframe src="' . $this->Url->build("/media/medias/index/$ref/$refId") . '" style="width:100%;" id="medias-' . $ref . '-' . $refId . '"></iframe>';
    }
}