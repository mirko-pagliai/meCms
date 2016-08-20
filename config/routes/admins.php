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

/**
 * Admin routes
 */
$routes->prefix('admin', function ($routes) {
    /**
     * Admin home page
     */
    $routes->connect(
        '/',
        ['controller' => 'Posts', 'action' => 'index'],
        ['_name' => 'dashboard']
    );

    /**
     * Other admin routes
     */
    $controllers = sprintf('(%s)', implode('|', [
        'backups',
        'banners',
        'banners_positions',
        'logs',
        'pages_categories',
        'pages',
        'photos_albums',
        'photos',
        'posts_categories',
        'posts_tags',
        'posts',
        'systems',
        'tags',
        'users',
        'users_groups',
    ]));

    $routes->connect('/:controller', [], ['controller' => $controllers]);
    $routes->connect('/:controller/:action/*', [], ['controller' => $controllers]);
});