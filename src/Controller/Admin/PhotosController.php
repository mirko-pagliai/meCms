<?php
/**
 * This file is part of me-cms.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-cms
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace MeCms\Controller\Admin;

use Cake\Event\Event;
use Cake\Http\Exception\InternalErrorException;
use MeCms\Controller\AppController;

/**
 * Photos controller
 * @property \MeCms\Model\Table\PhotosTable $Photos
 */
class PhotosController extends AppController
{
    /**
     * Called before the controller action.
     * You can use this method to perform logic that needs to happen before
     *   each controller action.
     * @param \Cake\Event\Event $event An Event instance
     * @return \Cake\Network\Response|null|void
     * @uses MeCms\Controller\AppController::beforeFilter()
     * @uses MeCms\Model\Table\PhotosAlbums::getList()
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        //Gets albums
        $albums = $this->Photos->Albums->getList();

        if ($albums->isEmpty()) {
            $this->Flash->alert(__d('me_cms', 'You must first create an album'));

            return $this->redirect(['controller' => 'PhotosAlbums', 'action' => 'index']);
        }

        $this->set(compact('albums'));
    }

    /**
     * Check if the provided user is authorized for the request
     * @param array $user The user to check the authorization of. If empty
     *  the user in the session will be used
     * @return bool `true` if the user is authorized, otherwise `false`
     * @uses MeCms\Controller\Component\AuthComponent::isGroup()
     */
    public function isAuthorized($user = null)
    {
        //Only admins and managers can delete photos
        return $this->request->isDelete() ? $this->Auth->isGroup(['admin', 'manager']) : true;
    }

    /**
     * Lists photos.
     *
     * This action can use the `index_as_grid` template.
     * @return \Cake\Network\Response|null|void
     * @uses MeCms\Model\Table\PhotosTable::queryFromFilter()
     */
    public function index()
    {
        $render = $this->request->getQuery('render');

        //The "render" type can also be set via cookies, if it's not set by query
        if (!$render && $this->Cookie->check('renderPhotos')) {
            $render = $this->Cookie->read('renderPhotos');
        }

        $query = $this->Photos->find()->contain(['Albums' => ['fields' => ['id', 'slug', 'title']]]);

        $this->paginate['order'] = ['Photos.created' => 'DESC'];

        //Sets the paginate limit and the maximum paginate limit
        //See http://book.cakephp.org/3.0/en/controllers/components/pagination.html#limit-the-maximum-number-of-rows-that-can-be-fetched
        if ($render === 'grid') {
            $this->paginate['limit'] = $this->paginate['maxLimit'] = getConfigOrFail('admin.photos');
        }

        $this->set('photos', $this->paginate($this->Photos->queryFromFilter($query, $this->request->getQueryParams())));

        if ($render) {
            $this->Cookie->write('renderPhotos', $render);

            if ($render === 'grid') {
                $this->render('index_as_grid');
            }
        }
    }

    /**
     * Uploads photos
     * @return void
     * @throws InternalErrorException
     * @uses MeCms\Controller\AppController::setUploadError()
     * @uses MeTools\Controller\Component\UploaderComponent
     */
    public function upload()
    {
        $album = $this->request->getQuery('album');
        $albums = $this->viewVars['albums']->toArray();

        //If there's only one available album
        if (!$album && count($albums) < 2) {
            $album = first_value(array_keys($albums));
            $this->request = $this->request->withQueryParams(compact('album'));
        }

        if ($this->request->getData('file')) {
            if (!$album) {
                throw new InternalErrorException(__d('me_cms', 'Missing ID'));
            }

            $uploaded = $this->Uploader->set($this->request->getData('file'))
                ->mimetype('image')
                ->save(PHOTOS . $album);

            if (!$uploaded) {
                $this->setUploadError($this->Uploader->getError());

                return;
            }

            $entity = $this->Photos->newEntity([
                'album_id' => $album,
                'filename' => basename($uploaded),
            ]);

            if ($entity->getErrors()) {
                $this->setUploadError(first_value(first_value($entity->getErrors())));

                return;
            }

            if (!$this->Photos->save($entity)) {
                $this->setUploadError(I18N_OPERATION_NOT_OK);
            }
        }
    }

    /**
     * Edits photo
     * @param string $id Photo ID
     * @return \Cake\Network\Response|null|void
     */
    public function edit($id = null)
    {
        $photo = $this->Photos->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $photo = $this->Photos->patchEntity($photo, $this->request->getData());

            if ($this->Photos->save($photo)) {
                $this->Flash->success(I18N_OPERATION_OK);

                return $this->redirect(['action' => 'index', $photo->album_id]);
            }

            $this->Flash->error(I18N_OPERATION_NOT_OK);
        }

        $this->set(compact('photo'));
    }

    /**
     * Downloads photo
     * @param string $id Photo ID
     * @return \Cake\Network\Response
     */
    public function download($id = null)
    {
        $file = $this->Photos->get($id)->path;

        return $this->response->withFile($file, ['download' => true]);
    }

    /**
     * Deletes photo
     * @param string $id Photo ID
     * @return \Cake\Network\Response|null|void
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $photo = $this->Photos->get($id);

        $this->Photos->deleteOrFail($photo);

        $this->Flash->success(I18N_OPERATION_OK);

        return $this->redirect(['action' => 'index', $photo->album_id]);
    }
}
