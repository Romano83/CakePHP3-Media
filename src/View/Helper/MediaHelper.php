<?php
namespace Media\View\Helper;

use Cake\Core\Configure;
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
	 * @param \Cake\View\View $View   The View this helper is being attached to.
	 * @param array           $config Configuration settings for the helper.
	 */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
    }

	/**
	 * Display TinyMCE editor
	 *
	 * @param string $fieldName Database field name
	 * @param string $ref       Table name
	 * @param int    $refId     Entity ID
	 * @param array  $options   FormHelper options
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

        $configure = Configure::check('Editor') ? Configure::read('Editor') : null;
        if ($configure) {
            $configure = json_encode($configure, JSON_PRETTY_PRINT);
        }

        $this->Html->scriptStart(['block' => true]);
        echo "initTinymce(" . $configure . ");";
        $this->Html->scriptEnd();

        return $this->textarea($fieldName, $ref, $refId, 'tinymce', $options);
    }

	/**
	 * Display CkEditor
	 *
	 * @param string $fieldName Database field name
	 * @param string $ref       Table name
	 * @param int    $refId     Entity ID
	 * @param array  $options   FormHelper options
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
	 * Find the resized image if exists or the original one
	 *
	 * @param string $image   Path to the image
	 * @param array  $options HtmlHelper image options
	 *
	 * @return string
	 */
    public function image($image, $options = [])
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

        return $this->Html->image($image, $options);
    }

	/**
	 * Display an iframe with media uploader/gallery
	 *
	 * @param string $ref   Table name
	 * @param int    $refId Entity ID
	 *
	 * @return string
     */
    public function iframe($ref, $refId)
    {
        return '<iframe src="' . $this->Url->build("/media/medias/index/$ref/$refId") . '" style="width:100%;" id="medias-' . $ref . '-' . $refId . '"></iframe>';
    }


	/**
	 * @param string      $fieldName Database field name
	 * @param string      $ref       Table name
	 * @param int         $refId     Entity ID
	 * @param bool|string $editor    Editor name
	 * @param array       $options   FormHelper options
	 *
	 * @return string
	 */
	private function textarea($fieldName, $ref, $refId, $editor = false, array $options = [])
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

}
