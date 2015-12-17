<?php
namespace Media\Model\Table;

use Cake\Event\Event;
use Cake\Network\Exception\NotImplementedException;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use Media\Model\Entity\Media;

/**
 * Medias Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Reves
 * @property \Cake\ORM\Association\HasMany $Actions
 */
class MediasTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config
     *            configuration for the Table.
     *            
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('medias');
        $this->displayField('id');
        $this->primaryKey('id');
    }

    /**
     * Delete uploaded files
     *
     * @param \Cake\Event\Event $event            
     * @param \Cake\ORM\Entity $entity            
     * @param \ArrayObject $options            
     *
     * @return bool
     */
    public function beforeDelete(Event $event, Entity $entity, \ArrayObject $options)
    {
        $file = $entity->file;
        $info = \pathinfo($file);
        foreach (glob(WWW_ROOT . $info['dirname'] . '/' . $info['filename'] . '_*x*.jpg') as $v) {
            @\unlink($v);
        }
        foreach (\glob(WWW_ROOT . $info['dirname'] . '/' . $info['filename'] . '.' . $info['extension']) as $v) {
            @\unlink($v);
        }
        return true;
    }

    /**
     * File treatment, upload and return string to save in database
     *
     * @param \Cake\Event\Event $event            
     * @param \Cake\ORM\Entity $entity            
     * @param \ArrayObject $options            
     *
     * @throws Cake\Network\Exception\NotImplementedException
     *
     * @return bool
     */
    public function beforeSave(Event $event, Entity $entity, \ArrayObject $options)
    {
        if (isset($entity->ref)) {
            $ref = $entity->ref;
            $table = TableRegistry::get($ref);
            if (! \in_array('Media', $table->behaviors()->loaded())) {
                throw new NotImplementedException(__d('media', "The model '{0}' doesn't have a 'Media' Behavior", $ref));
            }
        }
        if (isset($options['file']) && is_array($options['file']) && isset($entity->ref)) {
            $table = TableRegistry::get($entity->ref);
            $refId = $entity->ref_id;
            if (\method_exists($entity->ref, 'uploadMediasPath')) {
                $path = $table->uploadMediasPath($refId);
            } else {
                $path = $table->medias['path'];
            }
            $pathinfo = \pathinfo($options['file']['name']);
            $extension = \strtolower($pathinfo['extension']) == 'jpeg' ? 'jpg' : \strtolower($pathinfo['extension']);
            
            $filename = Inflector::slug($pathinfo['filename'], '-');
            $search = [
                '/',
                '%id',
                '%mid',
                '%cid',
                '%y',
                '%m',
                '%f'
            ];
            $replace = [
                DS,
                $refId,
                ceil($refId / 1000),
                ceil($refId / 100),
                date('Y'),
                date('m'),
                \strtolower(Inflector::slug($filename))
            ];
            $file = \str_replace($search, $replace, $path) . '.' . $extension;
            $this->testDuplicate($file);
            if (! \file_exists(\dirname(WWW_ROOT . $file))) {
                \mkdir(\dirname(WWW_ROOT . $file), 0777, true);
            }
            $this->moveUploadedFile($options['file']['tmp_name'], WWW_ROOT . $file);
            @\chmod(WWW_ROOT . $file, 0777);
            $entity->file = '/' . \trim(\str_replace(DS, '/', $file), '/');
        }
        return true;
    }
    
    /**
     * Resize images if enable in behavior options
     *
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Entity $entity
     * @param \ArrayObject $options
     */
    public function afterSave(Event $event, Entity $entity, \ArrayObject $options)
    {
        $table = TableRegistry::get($entity->ref);
        if (!\is_array($table->medias['resize'])) {
            return;
        }
        $this->resizeImage($entity->file, $table->medias['resize']);
        return true;
    }

    /**
     * Alias for move_uploded_file function
     *
     * @param string $filename            
     * @param string $destination            
     *
     * @return bool
     */
    protected function moveUploadedFile($filename, $destination)
    {
        return \move_uploaded_file($filename, $destination);
    }

    /**
     * Test if file $dir exists.
     * If it's the case, add a {n} before the extension
     *
     * @param string $dir            
     * @param int $count            
     *
     * @return string
     */
    protected function testDuplicate(&$dir, $count = 0)
    {
        $file = $dir;
        if ($count > 0) {
            $pathinfo = \pathinfo($dir);
            $file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $count . '.' . $pathinfo['extension'];
        }
        if (! \file_exists(WWW_ROOT . $file)) {
            $dir = $file;
            return $dir;
        } else {
            $count ++;
            $this->testDuplicate($dir, $count);
        }
    }
    
    /**
     * Resize image according an array of image sizes
     * @param string $path      path of the image
     * @param array $options    resize options
     */
    protected function resizeImage($path, array $options)
    {
        $path = \trim($path, '/');
        $pathinfo = \pathinfo(\trim($path, '/'));
        if (!\in_array($pathinfo['extension'], ['jpg', 'jpeg', 'gif', 'png'])) {
            return;
        }
        foreach ($options['sizes'] as $size) {
            $width = \explode('x', $size)[0];
            $height = \explode('x', $size)[1];
            $output = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_'. $width . 'x' . $height . '.' . $pathinfo['extension'];
            if (!\file_exists($output)) {
                $info = \getimagesize($path);
                list($oldWidth, $oldHeight) = $info;
                switch ($info[2]) {
                    case IMAGETYPE_GIF: $image = \imagecreatefromgif($path); break;
                    case IMAGETYPE_JPEG : $image = \imagecreatefromjpeg($path); break;
                    case IMAGETYPE_PNG : $image = \imagecreatefrompng($path); break;
                    default : return false;
                }
                $widthRatio = $oldWidth / $width;
                $heightRatio = $oldHeight / $height;
    
                $optimalRatio = $widthRatio;
                if ($heightRatio < $widthRatio) {
                    $optimalRatio = $heightRatio;
                }
                $widthCrop = ($oldWidth / $optimalRatio);
                $heightCrop = ($oldHeight / $optimalRatio);
    
                $imageCrop      = \imagecreatetruecolor($widthCrop, $heightCrop);
                $imageResized   = \imagecreatetruecolor($width, $height);
    
                if ($info[2] == IMAGETYPE_GIF || $info[2] == IMAGETYPE_PNG) {
                    $transparency = \imagecolortransparent($image);
                    if ($transparency >= 0) {
                        $transparencyIndex = \imagecolorat($image, 0, 0);
                        $transparencyColor = \imagecolorsforindex($image, $transparencyIndex);
                        $transparency = \imagecolorallocate($imageCrop, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
                        \imagefill($imageCrop, 0, 0, $transparency);
                        \imagecolortransparent($imageCrop, $transparency);
                        \imagefill($imageResized, 0, 0, $transparency);
                        \imagecolortransparent($imageResized, $transparency);
                    } elseif ($info[2] == IMAGETYPE_PNG) {
                        \imagealphablending($imageCrop, false);
                        \imagealphablending($imageResized, false);
                        $color = \imagecolorallocatealpha($imageCrop, 0, 0, 0, 127);
                        \imagefill($imageCrop, 0, 0, $color);
                        \imagesavealpha($imageCrop, true);
                        \imagefill($imageResized, 0, 0, 127);
                        \imagesavealpha($imageResized, true);
                    }
                }
    
                \imagecopyresampled($imageCrop, $image, 0, 0, 0, 0, $widthCrop, $heightCrop, $oldWidth, $oldHeight);
                \imagecopyresampled($imageResized, $image, 0, 0, ($widthCrop - $width) / 2, ($heightCrop - $height) / 2, $width, $height, $width, $height);
    
                switch ($info[2]) {
                    case IMAGETYPE_GIF: \imagegif(($options['crop'])? $imageResized : $imageCrop, $output, $options['quality']); break;
                    case IMAGETYPE_JPEG: \imagejpeg(($options['crop'])? $imageResized : $imageCrop, $output, $options['quality']); break;
                    case IMAGETYPE_PNG: \imagepng(($options['crop'])? $imageResized : $imageCrop, $output, 9); break;
                    default: return false;
                }
            }
        }
    }

    /**
     * Validate file before save entity
     *
     * @param \Cake\Validation\Validator $validator            
     *
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->add('file', [
            'global' => [
                'rule' => function ($data, $provider) {
                    $name = is_array($data) ? $data['file']['name'] : $data;
                    $table = TableRegistry::get($provider['data']['ref']);
                    $pathinfo = \pathinfo($name);
                    $extension = \strtolower($pathinfo['extension']) == 'jpeg' ? 'jpg' : \strtolower($pathinfo['extension']);
                    // Extensions validation
                    if (! \in_array($extension, $table->medias['extensions'])) {
                        return __d('media', "You don't have the permission to upload this filetype ({0} only)", \implode(', ', $table->medias['extensions']));
                    }
                    // File number validation
                    if ($table->medias['limit'] > 0 && $provider['data']['ref_id'] > 0) {
                        $qty = $this->find()
                            ->where([
                            'ref' => $provider['data']['ref'],
                            'ref_id' => $provider['data']['ref_id']
                        ])
                            ->count();
                        if ($qty >= $table->medias['limit']) {
                            return __d('media', "You can't send more than {0} files", $table->medias['limit']);
                        }
                    }
                    // Height and width validation (for png/jpg/gif/tiff)
                    if (\in_array($extension, [
                        'jpg',
                        'png',
                        'gif',
                        'tiff'
                    ]) && $table->medias['max_width'] > 0 || $table->medias['max_height'] > 0) {
                        list ($width, $height) = \getimagesize($data['file']['tmp_name']);
                        if ($table->medias['max_width'] > 0 && $width > $table->medias['max_width']) {
                            return __d('media', "The width is too big, it must be less than {0}px", $table->medias['max_width']);
                        }
                        if ($table->medias['max_height'] > 0 && $height > $table->medias['max_height']) {
                            return __d('media', "The height is too big, it must be less than {0}px", $table->medias['max_height']);
                        }
                    }
                    // File size validation
                    if ($table->medias['size'] > 0 && \floor($data['file']['size'] / 1024 > $table->medias['size'])) {
                        $humanSize = $table->medias['size'] > 1024 ? round($table->medias['size'] / 1024, 1) . ' Mo' : $table->medias['size'] . ' Ko';
                        return __d('media', "The file size is too big, {0} max", $humanSize);
                    }
                    return true;
                }
            ]
        ]);
        return $validator;
    }
}
