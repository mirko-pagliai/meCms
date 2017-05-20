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
namespace MeCms\Test\TestCase\Controller\Admin;

use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use MeCms\Controller\Admin\UsersGroupsController;
use MeCms\TestSuite\Traits\AuthMethodsTrait;

/**
 * UsersGroupsControllerTest class
 */
class UsersGroupsControllerTest extends IntegrationTestCase
{
    use AuthMethodsTrait;

    /**
     * @var \MeCms\Controller\Admin\UsersGroupsController
     */
    protected $Controller;

    /**
     * @var \MeCms\Model\Table\UsersGroupsTable
     */
    protected $UsersGroups;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms.users_groups',
    ];

    /**
     * @var array
     */
    protected $url;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUserGroup('admin');

        $this->Controller = new UsersGroupsController;

        $this->UsersGroups = TableRegistry::get('MeCms.UsersGroups');

        Cache::clear(false, $this->UsersGroups->cache);

        $this->url = ['controller' => 'UsersGroups', 'prefix' => ADMIN_PREFIX, 'plugin' => ME_CMS];
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Controller, $this->UsersGroups);
    }

    /**
     * Tests for `isAuthorized()` method
     * @test
     */
    public function testIsAuthorized()
    {
        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => false,
            'user' => false,
        ]);
    }

    /**
     * Tests for `index()` method
     * @test
     */
    public function testIndex()
    {
        $this->get(array_merge($this->url, ['action' => 'index']));
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Admin/UsersGroups/index.ctp');

        $groupsFromView = $this->viewVariable('groups');
        $this->assertInstanceof('Cake\ORM\ResultSet', $groupsFromView);
        $this->assertNotEmpty($groupsFromView);

        foreach ($groupsFromView as $group) {
            $this->assertInstanceof('MeCms\Model\Entity\UsersGroup', $group);
        }
    }

    /**
     * Tests for `add()` method
     * @test
     */
    public function testAdd()
    {
        $url = array_merge($this->url, ['action' => 'add']);

        $this->get($url);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Admin/UsersGroups/add.ctp');

        $groupFromView = $this->viewVariable('group');
        $this->assertInstanceof('MeCms\Model\Entity\UsersGroup', $groupFromView);
        $this->assertNotEmpty($groupFromView);

        //POST request. Data are valid
        $this->post($url, ['name' => 'team', 'label' => 'Team']);
        $this->assertRedirect(['action' => 'index']);
        $this->assertSession('The operation has been performed correctly', 'Flash.flash.0.message');

        //POST request. Data are invalid
        $this->post($url, ['name' => 'aa']);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertResponseContains('The operation has not been performed correctly');

        $groupFromView = $this->viewVariable('group');
        $this->assertInstanceof('MeCms\Model\Entity\UsersGroup', $groupFromView);
        $this->assertNotEmpty($groupFromView);
    }

    /**
     * Tests for `edit()` method
     * @test
     */
    public function testEdit()
    {
        $url = array_merge($this->url, ['action' => 'edit', 2]);

        $this->get($url);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertTemplate(ROOT . 'src/Template/Admin/UsersGroups/edit.ctp');

        $groupFromView = $this->viewVariable('group');
        $this->assertInstanceof('MeCms\Model\Entity\UsersGroup', $groupFromView);
        $this->assertNotEmpty($groupFromView);

        //POST request. Data are valid
        $this->post($url, ['description' => 'This is a description']);
        $this->assertRedirect(['action' => 'index']);
        $this->assertSession('The operation has been performed correctly', 'Flash.flash.0.message');

        //POST request. Data are invalid
        $this->post($url, ['label' => 'aa']);
        $this->assertResponseOk();
        $this->assertResponseNotEmpty();
        $this->assertResponseContains('The operation has not been performed correctly');

        $groupFromView = $this->viewVariable('group');
        $this->assertInstanceof('MeCms\Model\Entity\UsersGroup', $groupFromView);
        $this->assertNotEmpty($groupFromView);
    }

    /**
     * Tests for `delete()` method
     * @test
     */
    public function testDelete()
    {
        $url = array_merge($this->url, ['action' => 'delete']);

        $id = $this->UsersGroups->find()
            ->where(['id <=' => 3, 'user_count' => 0])
            ->extract('id')
            ->first();

        //Cannot delete a default group
        $this->post(array_merge($url, [$id]));
        $this->assertRedirect(['action' => 'index']);
        $this->assertSession('You cannot delete this users group', 'Flash.flash.0.message');

        $id = $this->UsersGroups->find()
            ->where(['id >' => 3, 'user_count >' => 0])
            ->extract('id')
            ->first();

        $this->post(array_merge($url, [$id]));
        $this->assertRedirect(['action' => 'index']);
        $this->assertSession(
            'Before deleting this, you must delete or reassign all items that belong to this element',
            'Flash.flash.0.message'
        );

        $id = $this->UsersGroups->find()
            ->where(['id >' => 3, 'user_count' => 0])
            ->extract('id')
            ->first();

        $this->post(array_merge($url, [$id]));
        $this->assertRedirect(['action' => 'index']);
        $this->assertSession('The operation has been performed correctly', 'Flash.flash.0.message');
    }
}
