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
     * @param \Cake\View\View $View
     *            The View this helper is being attached to.
     * @param array $config
     *            Configuration settings for the helper.
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
    }

    /**
     * Display TinyMCE editor
     *
     * @param string $fieldName
     *            Database field name
     * @param string $ref
     *            Table name
     * @param int $refId
     *            Entity ID
     * @param array $options
     *            FormHelper options
     *            
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
     * Display CkEditor
     *
     * @param string $fieldName
     *            Database field name
     * @param string $ref
     *            Table name
     * @param int $refId
     *            Entity ID
     * @param array $options
     *            FormHelper options
     *            
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
     *            Database field name
     * @param string $ref
     *            Table name
     * @param int $refId
     *            Entity ID
     * @param bool|string $editor
     *            Editor name
     * @param array $options
     *            FormHelper options
     *            
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
     * Find the resized image if exists or the original one
     *
     * @param string $image
     *          path to the image
     * @param array $options
     *         array of options
     */
    public function image($image, $options = [])
    {
        $width = isset($options['width']) ? $options['width'] : null;
        $height = isset($options['height']) ? $options['height'] : null;
        if ($width && $height) {
            $pathinfo = \pathinfo($image);
            $newImg = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_' . $width . 'x' . $height . '.' . $pathinfo['extension'];
            if (\file_exists(\trim($newImg, '/'))) {
                $image = $newImg;
            }
        }
        return $this->Html->image($image, $options);
    }

    /**
     * Display an iframe with media uploader/gallery
     *
     * @param string $ref
     *            Table name
     * @param int $refId
     *            Entity ID
     *            
     * @return string
     */
    public function iframe($ref, $refId)
    {
        return '<iframe src="' . $this->Url->build("/media/medias/index/$ref/$refId") . '" style="width:100%;min-width:600px;" id="medias-' . $ref . '-' . $refId . '"></iframe>';
    }
}
