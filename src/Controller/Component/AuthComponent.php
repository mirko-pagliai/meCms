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
 * @see         http://api.cakephp.org/3.3/class-Cake.Controller.Component.AuthComponent.html
 */
namespace MeCms\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Component\AuthComponent as CakeAuthComponent;

/**
 * Authentication control component class.
 *
 * Binds access control with user authentication and session management.
 *
 * Rewrites {@link http://api.cakephp.org/3.3/class-Cake.Controller.Component.AuthComponent.html AuthComponent}.
 */
class AuthComponent extends CakeAuthComponent
{
    /**
     * Constructor
     * @param ComponentRegistry $registry A ComponentRegistry this component
     *  can use to lazy load its components
     * @param array $config Array of configuration settings
     * @return void
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        //Sets config
        $config = am([
            'authenticate' => [
                'Form' => ['contain' => 'Groups', 'userModel' => 'MeCms.Users'],
            ],
            'authError' => __d('me_cms', 'You are not authorized for this action'),
            'authorize' => 'Controller',
            'flash' => ['element' => 'MeTools.error'],
            'loginAction' => ['_name' => 'login'],
            'loginRedirect' => ['_name' => 'dashboard'],
            'logoutRedirect' => ['_name' => 'homepage'],
            'unauthorizedRedirect' => ['_name' => 'dashboard'],
        ], $config);

        parent::__construct($registry, $config);
    }

    /**
     * Constructor hook method
     * @param array $config The configuration settings provided to this
     *  component
     * @return void
     * @see http://api.cakephp.org/3.3/class-Cake.Controller.Component.html#_initialize
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        //The authorization error is shown only if the user is already logged
        //  in and he is trying to do something not allowed
        if (!$this->user('id')) {
            $this->config('authError', false);
        }
    }

    /**
     * Checks whether the logged user has a specific ID.
     *
     * You can pass the ID as string or array of IDS.
     * In the last case, it will be sufficient that the user has one of the IDS.
     * @param string|array $id User ID as string or array
     * @return bool
     */
    public function hasId($id)
    {
        if (!$this->user('id')) {
            return false;
        }

        return in_array($this->user('id'), (array)$id);
    }

    /**
     * Checks whether the logged user is the admin founder (ID 1)
     * @return bool
     */
    public function isFounder()
    {
        if (!$this->user('id')) {
            return false;
        }

        return $this->user('id') === 1;
    }

    /**
     * Checks whether the user is logged in
     * @return bool
     */
    public function isLogged()
    {
        return !empty($this->user('id'));
    }

    /**
     * Checks whether the logged user belongs to a group.
     *
     * You can pass the group as string or array of groups.
     * In the last case, it will be sufficient that the user belongs to one of
     *  the groups.
     * @param string|array $group User group as string or array
     * @return bool
     */
    public function isGroup($group)
    {
        if (!$this->user('group.name')) {
            return false;
        }

        return in_array($this->user('group.name'), (array)$group);
    }
}
