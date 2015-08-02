<?php
namespace Media\Controller;

use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;

class MediasController extends AppController
{

    /**
     *
     * @param Cake\ORM\Table $ref            
     * @param int $refId            
     *
     * @return bool
     */
    public function canUploadMedias($ref, $refId)
    {
        if (method_exists('App\Controller\AppController', 'canUploadMedias')) {
            return \App\Controller\AppController::canUploadMedias($ref, $refId);
        } else {
            return false;
        }
    }

    /**
     *
     * @param \Cake\Event\Event $event            
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->layout = 'uploader';
        if (in_array('Security', $this->components()->loaded())) {
            $this->Security->config('unlockedActions', [ 'index', 'edit', 'upload', 'order', 'thumb', 'update', 'delete' ]);
        }
    }

    /**
     *
     * @param Cake\ORM\Entity $ref            
     * @param int $refId            
     *
     * @throws Cake\Network\Exception\ForbiddenException
     *
     * @return array
     */
    public function index($ref, $refId)
    {
        if (! $this->canUploadMedias($ref, $refId)) {
            throw new ForbiddenException();
        }
        $this->loadModel($ref);
        $this->set(compact('ref', 'refId'));
        if (! in_array('Media', $this->$ref->Behaviors()->loaded())) {
            return $this->render('nobehavior');
        }
        $id = isset($this->request->query['id']) ? $this->request->query['id'] : false;
        $medias = $this->Medias->find('all', [
            'conditions' => [
                'ref_id' => $refId,
                'ref' => $ref
            ]
        ])->toArray();
        $medias = ! empty($medias) ? $medias : null;
        $thumbID = false;
        if ($this->$ref->hasField('media_id')) {
            $entity = $this->$ref->get($refId);
            $thumbID = $entity->media_id;
        }
        $extensions = $this->$ref->medias['extensions'];
        $editor = isset($this->request->query['editor']) ? $this->request->query['editor'] : false;
        $this->set(compact('id', 'medias', 'thumbID', 'editor', 'extensions'));
    }

    /**
     *
     * @param int|null $id            
     *
     * @throws \Cake\Network\Exception\NotFoundException
     * @throws \Cake\Network\Exception\ForbiddenException
     *
     * @return void
     */
    public function edit($id = null)
    {
        $id = $this->request->query['media_id'];
        $data = [];
        if ($id) {
            $media = $this->Medias->find()
                ->where([
                'id' => $id
            ])
                ->first();
            if (! $media) {
                throw new NotFoundException();
            }
            if (! $this->canUploadMedias($media->ref, $media->ref_id)) {
                throw new ForbiddenException();
            }
            $data['src'] = $media['file'];
            $data['alt'] = basename($media['file']);
            $data['class'] = '';
            $data['caption'] = $media['caption'];
            $data['editor'] = isset($this->request->query['editor']) ? $this->request->query['editor'] : false;
            $data['ref'] = $media->ref;
            $data['ref_id'] = $media->ref_id;
            $data['type'] = $media->file_type;
        }
        $data = \array_merge($data, $this->request->query);
        $this->set(compact('data'));
    }

    /**
     *
     * @param Cake\ORM\Entity $ref            
     * @param int $refId            
     *
     * @throws \Cake\Network\Exception\ForbiddenException
     *
     * @return void
     */
    public function upload($ref, $refId)
    {
        if (! $this->canUploadMedias($ref, $refId)) {
            throw new ForbiddenException();
        }
        $this->autoRender = false;
        $data = [
            'ref' => $ref,
            'ref_id' => $refId,
            'file' => $this->request->data
        ];
        $media = $this->Medias->newEntity();
        $media = $this->Medias->patchEntity($media, $data, [
            'validate' => 'default'
        ]);
        if ($media->errors()) {
            echo json_encode([
                'error' => $media->errors()
            ]);
            return;
        } else {
            $media = $this->Medias->save($media, $this->request->data);
        }
        $this->loadModel($ref);
        $thumbID = $this->$ref->hasField('media_id');
        $editor = isset($this->request->query['editor']) ? $this->request->query['editor'] : false;
        $id = isset($this->request->query['id']) ? $this->request->query['id'] : false;
        $this->set(\compact('media', 'thumbID', 'editor', 'id'));
        $this->layout = 'json';
        $this->render('media');
    }

    /**
     *
     * @param int $id            
     *
     * @throws \Cake\Network\Exception\BadRequestException
     * @throws \Cake\Network\Exception\NotFoundException
     * @throws \Cake\Network\Exception\ForbiddenException
     *
     * @return void
     */
    public function update($id)
    {
        if (! $this->request->is('ajax')) {
            throw new BadRequestException();
        }
        $this->autoRender = false;
        if ($this->request->is([
            'put',
            'post'
        ])) {
            $media = $this->Medias->find()
                ->where([
                'id' => $id
            ])
                ->first();
            if (! $media) {
                throw new NotFoundException();
            }
            if (! $this->canUploadMedias($media->ref, $media->ref_id)) {
                throw new ForbiddenException();
            }
            $data = [];
            $data['name'] = $this->request->data['name'] ? $this->request->data['name'] : null;
            $data['caption'] = $this->request->data['caption'] ? $this->request->data['caption'] : null;
            $media = $this->Medias->patchEntity($media, $data, [
                'validate' => false
            ]);
            $this->Medias->save($media);
        }
    }

    /**
     *
     * @param int $id            
     *
     * @throws \Cake\Network\Exception\BadRequestException
     * @throws \Cake\Network\Exception\NotFoundException
     * @throws \Cake\Network\Exception\ForbiddenException
     *
     * @return void
     */
    public function delete($id)
    {
        $this->autoRender = false;
        if (! $this->request->is('ajax')) {
            throw new BadRequestException();
        }
        $media = $this->Medias->find()
            ->where([
            'id' => $id
        ])
            ->first();
        if (! $media) {
            throw new NotFoundException();
        }
        if (! $this->canUploadMedias($media->ref, $media->ref_id)) {
            throw new ForbiddenException();
        }
        $this->Medias->delete($media, [
            'atomic' => false
        ]);
    }

    /**
     *
     * @param int $id            
     *
     * @throws \Cake\Network\Exception\NotFoundException
     * @throws \Cake\Network\Exception\ForbiddenException
     *
     * @return void
     */
    public function thumb($id)
    {
        $media = $this->Medias->find()
            ->select([
            'ref',
            'ref_id'
        ])
            ->where([
            'id' => $id
        ])
            ->first();
        if (! $media) {
            throw new NotFoundException();
        }
        $ref = $media->ref;
        $refId = $media->ref_id;
        if (! $this->canUploadMedias($ref, $refId)) {
            throw new ForbiddenException();
        }
        $this->loadModel($ref);
        $entity = $this->$ref->get($refId);
        $entity->media_id = $id;
        $this->$ref->save($entity);
        $this->redirect([
            'action' => 'index',
            $ref,
            $refId
        ]);
    }

    /**
     *
     * @throws \Cake\Network\Exception\ForbiddenException
     *
     * @return void
     */
    public function order()
    {
        $this->layout = null;
        $this->autoRender = false;
        if (! $this->request->is('ajax')) {
            throw new BadRequestException();
        }
        if (! empty($this->request->data['Media'])) {
            $id = key($this->request->data['Media']);
            $media = $this->Medias->get($id, [
                'fields' => [
                    'ref',
                    'ref_id'
                ]
            ]);
            if (! $this->canUploadMedias($media->ref, $media->ref_id)) {
                throw new ForbiddenException();
            }
            foreach ($this->request->data['Media'] as $k => $v) {
                $media = $this->Medias->get($k);
                $media->position = $v;
                $media = $this->Medias->save($media, [
                    'validate' => false
                ]);
            }
        }
    }
}
