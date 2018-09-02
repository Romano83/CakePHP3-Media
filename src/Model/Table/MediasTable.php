<?php
namespace Media\Model\Table;

use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Network\Exception\NotImplementedException;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use Intervention\Image\Constraint;
use Intervention\Image\Exception\InvalidArgumentException;
use Intervention\Image\Exception\MissingDependencyException;
use Intervention\Image\Exception\NotWritableException;
use Intervention\Image\ImageManager;
use Spatie\ImageOptimizer\OptimizerChainFactory;

/**
 * Medias Model
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
        $this->setTable('medias');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
     * @throws \Cake\Network\Exception\NotImplementedException
     *
     * @return bool
     */
    public function beforeSave(Event $event, Entity $entity, \ArrayObject $options)
    {
        $table = TableRegistry::get($entity->ref);
        if (\is_array($table->medias['resize'])) {
	        $this->resizeImage($entity->file, $table->medias['resize']);
	        return true;
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
            
            $filename = Text::slug($pathinfo['filename'], '-');
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
                \strtolower(Text::slug($filename))
            ];
            $file = \str_replace($search, $replace, $path) . '.' . $extension;
            $this->testDuplicate($file);
            if (! \file_exists(\dirname(WWW_ROOT . $file))) {
                \mkdir(\dirname(WWW_ROOT . $file), 0777, true);
            }
            move_uploaded_file($options['file']['tmp_name'], WWW_ROOT . $file);
            @\chmod(WWW_ROOT . $file, 0777);
            $entity->file = '/' . \trim(\str_replace(DS, '/', $file), '/');
        }
        return true;
    }

	/**
	 * Resize images if enable in behavior options
	 *
	 * @param \Cake\Event\Event $event
	 * @param \Cake\ORM\Entity  $entity
	 * @param \ArrayObject      $options
	 *
	 * @return bool
	 */
	public function afterSave(Event $event, Entity $entity, \ArrayObject $options)
	{
		$table = TableRegistry::get($entity->ref);
		if (\is_array($table->medias['resize'])) {
			$this->resizeImage($entity->file, $table->medias['resize']);
			return true;
		}
		$this->optimizeImage($entity->file);
		return true;
	}

	/**
	 * Resize image according an array of image sizes
	 *
	 * @param string $path    path of the image
	 * @param array  $options resize options
	 *
	 * @return bool
	 */
	private function resizeImage($path, array $options)
	{
		$path = \trim($path, '/');
		$pathinfo = \pathinfo(\trim($path, '/'));
		if (!\in_array($pathinfo['extension'], ['jpg', 'jpeg', 'gif', 'png'])) {
			return false;
		}
		$managerConfiguration = [];
		if (extension_loaded('imagick')) {
			$managerConfiguration = ['driver' => 'imagick'];
		}
		try {
			$manager = new ImageManager($managerConfiguration);
			foreach ($options['sizes'] as $size) {
				$width  = $size['width'] ?? null;
				$height = $size['height'] ?? null;
				$crop   = $size['crop'] ?? false;
				$output = $pathinfo['dirname'] . DS . $pathinfo['filename'] . '_'
				          . $width . 'x' . $height . '.' . $pathinfo['extension'];
				if ( ! file_exists( $output ) ) {
					$image = $manager->make( $path );
					if ( ! $crop ) {
						try {
							$image->resize( $width, $height, function(Constraint $constraint) {
								$constraint->aspectRatio();
								$constraint->upsize();
							} );
						} catch (InvalidArgumentException $e) {
							Log::error(__METHOD__ . ' ' . $e->getMessage());
						}
					} else {
						$image->crop( $width, $height );
					}
					try {
						$image->save( $output, $options['quality'] ?? 60 );
					} catch (NotWritableException $e) {
						Log::error(__METHOD__ . ' ' . $e->getMessage());
					}
					$image->destroy();
					$this->optimizeImage($output);
				}
			}
		} catch (MissingDependencyException $e) {
			Log::error(__METHOD__ . ' ' . $e->getMessage());
		}
		return true;
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
    private function testDuplicate(&$dir, $count = 0)
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
                    $extension = (new File($name, false))->ext();
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
                        $humanSize = Number::toReadableSize($table->medias['size']) > 1024 ? round($table->medias['size'] / 1024, 1) . ' Mo' : $table->medias['size'] . ' Ko';
                        return __d('media', "The file size is too big, {0} max", $humanSize);
                    }
                    return true;
                }
            ]
        ]);
        return $validator;
    }

	/**
	 * @param string $path path to the file
	 *
	 * @return void
	 */
	private function optimizeImage( $path ) {
		$path = \trim($path, '/');
		$pathinfo = \pathinfo(\trim($path, '/'));
		if (!\in_array($pathinfo['extension'], ['jpg', 'jpeg', 'gif', 'png'])) {
			return;
		}
		$optimizerChain = OptimizerChainFactory::create();
		try {
			$optimizerChain->setTimeout(5)->optimize($path);
		} catch (\Exception $e) {
			Log::error(__METHOD__ . ' ' . $e->getMessage());
		}
    }
}
