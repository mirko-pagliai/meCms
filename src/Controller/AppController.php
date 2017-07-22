<?php
/**
 * This file is part of MeCms.
 *
 * MeCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeCms.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeCms\Controller;

use App\Controller\AppController as BaseController;
use Cake\Event\Event;
use Cake\I18n\I18n;

/**
 * Application controller class
 */
class AppController extends BaseController
{
    /**
     * Called before the controller action.
     * You can use this method to perform logic that needs to happen before
     *  each controller action.
     * @param \Cake\Event\Event $event An Event instance
     * @return \Cake\Network\Response|null|void
     * @see http://api.cakephp.org/3.4/class-Cake.Controller.Controller.html#_beforeFilter
     * @uses App\Controller\AppController::beforeFilter()
     * @uses isBanned()
     * @uses isOffline()
     */
    public function beforeFilter(Event $event)
    {
        //Checks if the site is offline
        if ($this->isOffline()) {
            return $this->redirect(['_name' => 'offline']);
        }

        //Checks if the user's IP address is banned
        if ($this->isBanned()) {
            return $this->redirect(['_name' => 'ipNotAllowed']);
        }

        //Authorizes the current action, if this is not an admin request
        if (!$this->request->isAdmin()) {
            $this->Auth->allow();
        }

        //Adds the current sort field in the whitelist of pagination
        if ($this->request->isAdmin() && $this->request->getQuery('sort')) {
            $this->paginate['sortWhitelist'] = [$this->request->getQuery('sort')];
        }

        //Sets the paginate limit and the maximum paginate limit
        //See http://book.cakephp.org/3.0/en/controllers/components/pagination.html#limit-the-maximum-number-of-rows-that-can-be-fetched
        $this->paginate['limit'] = getConfigOrFail('default.records');

        if ($this->request->isAdmin()) {
            $this->paginate['limit'] = getConfigOrFail('admin.records');
        }

        $this->paginate['maxLimit'] = $this->paginate['limit'];

        //Layout for ajax and json requests
        if ($this->request->is(['ajax', 'json'])) {
            $this->viewBuilder()->setLayout(ME_CMS . '.ajax');
        }

        $this->viewBuilder()->setClassName(ME_CMS . '.View/App');

        //Uses a custom View class (`MeCms.AppView` or `MeCms.AdminView`)
        if ($this->request->isAdmin()) {
            $this->viewBuilder()->setClassName(ME_CMS . '.View/Admin');
        }

        parent::beforeFilter($event);
    }

    /**
     * Called after the controller action is run, but before the view is
     *  rendered.
     * You can use this method to perform logic or set view variables that are
     *  required on every request.
     * @param \Cake\Event\Event $event An Event instance
     * @return void
     * @see http://api.cakephp.org/3.4/class-Cake.Controller.Controller.html#_beforeRender
     * @uses App\Controller\AppController::beforeRender()
     */
    public function beforeRender(Event $event)
    {
        //Loads the `Auth` helper.
        //The `helper is loaded here (instead of the view) to pass user data
        $this->viewBuilder()->setHelpers([ME_CMS . '.Auth' => $this->Auth->user()]);

        parent::beforeRender($event);
    }

    /**
     * Initialization hook method
     * @return void
     * @uses App\Controller\AppController::initialize()
     */
    public function initialize()
    {
        //Loads components
        //The configuration for `AuthComponent`  takes place in the same class
        $this->loadComponent('Cookie', ['encryption' => false]);
        $this->loadComponent(ME_CMS . '.Auth');
        $this->loadComponent(METOOLS . '.Flash');
        $this->loadComponent('RequestHandler');
        $this->loadComponent(METOOLS . '.Uploader');
        $this->loadComponent('Recaptcha.Recaptcha', [
            'sitekey' => getConfigOrFail('Recaptcha.public'),
            'secret' => getConfigOrFail('Recaptcha.private'),
            'lang' => substr(I18n::locale(), 0, 2),
        ]);

        parent::initialize();
    }

    /**
     * Checks if the user is authorized for the request
     * @param array $user The user to check the authorization of. If empty
     *  the user in the session will be used
     * @return bool `true` if the user is authorized, otherwise `false`
     * @uses MeCms\Controller\Component\AuthComponent::isGroup()
     */
    public function isAuthorized($user = null)
    {
        //Any registered user can access public functions
        if (!$this->request->getParam('prefix')) {
            return true;
        }

        //Only admin and managers can access all admin actions
        if ($this->request->isAdmin()) {
            return $this->Auth->isGroup(['admin', 'manager']);
        }

        //Default deny
        return false;
    }

    /**
     * Checks if the user's IP address is banned
     * @return bool
     * @since 2.15.2
     */
    protected function isBanned()
    {
        return $this->request->isBanned() && !$this->request->isAction('ipNotAllowed', 'Systems');
    }

    /**
     * Checks if the site is offline
     * @return bool
     * @since 2.15.2
     */
    protected function isOffline()
    {
        return $this->request->isOffline();
    }

    /**
     * Internal method to set an upload error.
     *
     * It saves the error as view var that `JsonView` should serialize and sets
     *  the response status code to 500.
     * @param string $error Error message
     * @return void
     * @since 2.18.1
     */
    protected function setUploadError($error)
    {
        $this->response = $this->response->withStatus(500);

        $this->set(compact('error'));
        $this->set('_serialize', ['error']);
    }
}
