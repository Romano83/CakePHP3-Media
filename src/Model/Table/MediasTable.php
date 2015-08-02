<?php
namespace Media\Model\Table;

use Cake\Event\Event;
use Cake\Network\Exception\NotImplementedException;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
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
     *            The configuration for the Table.
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
     * @throws Cake\Network\Exception\NotImplementedException
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
            $search = ['/', '%id', '%mid', '%cid', '%y', '%m', '%f' ];
            $replace = [ DS, $refId, ceil($refId / 1000), ceil($refId / 100), date('Y'), date('m'), \strtolower(Inflector::slug($filename)) ];
            $file = \str_replace($search, $replace, $path) . '.' . $extension;
            $this->testDuplicate($file);
            if (! \file_exists(\dirname(WWW_ROOT . $file))) {
                \mkdir(\dirname(WWW_ROOT . $file), 0777, true);
            }
            $this->move_uploaded_file($options['file']['tmp_name'], WWW_ROOT . $file);
            @\chmod(WWW_ROOT . $file, 0777);
            $entity->file = '/' . \trim(\str_replace(DS, '/', $file), '/');
        }
        return true;
    }

    /**
     * Alias for move_uploded_file function
     *
     * @param string $filename
     * @param string $destination
     * @return bool
     */
    protected function move_uploaded_file($filename, $destination)
    {
        return \move_uploaded_file($filename, $destination);
    }

    /**
     * Test if file $dir exists.
     * If it's the case, add a {n} before the extension
     *
     * @param string $dir
     * @param int $count
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
     * Validate file before save entity
     *
     * @param \Cake\Validation\Validator $validator
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
