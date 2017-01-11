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
namespace MeCms\Test\TestCase\Model\Table;

use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * PostsTableTest class
 */
class PostsTagsTableTest extends TestCase
{
    /**
     * @var \MeCms\Model\Table\PostsTagsTable
     */
    protected $PostsTags;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms.posts',
        'plugin.me_cms.posts_tags',
        'plugin.me_cms.tags',
    ];

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->PostsTags = TableRegistry::get('MeCms.PostsTags');

        Cache::clear(false, $this->PostsTags->cache);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->PostsTags);
    }

    /**
     * Test for `cache` property
     * @test
     */
    public function testCacheProperty()
    {
        $this->assertEquals('posts', $this->PostsTags->cache);
    }

    /**
     * Test for `buildRules()` method
     * @test
     */
    public function testBuildRules()
    {
        $entity = $this->PostsTags->newEntity([
            'tag_id' => 999,
            'post_id' => 999,
        ]);
        $this->assertFalse($this->PostsTags->save($entity));

        $this->assertEquals([
            'tag_id' => ['_existsIn' => 'You have to select a valid option'],
            'post_id' => ['_existsIn' => 'You have to select a valid option'],
        ], $entity->errors());
    }

    /**
     * Test for `initialize()` method
     * @test
     */
    public function testInitialize()
    {
        $this->assertEquals('posts_tags', $this->PostsTags->table());
        $this->assertEquals('id', $this->PostsTags->displayField());
        $this->assertEquals('id', $this->PostsTags->primaryKey());

        $this->assertEquals('Cake\ORM\Association\BelongsTo', get_class($this->PostsTags->Posts));
        $this->assertEquals('post_id', $this->PostsTags->Posts->foreignKey());
        $this->assertEquals('INNER', $this->PostsTags->Posts->joinType());
        $this->assertEquals('MeCms.Posts', $this->PostsTags->Posts->className());

        $this->assertEquals('Cake\ORM\Association\BelongsTo', get_class($this->PostsTags->Tags));
        $this->assertEquals('tag_id', $this->PostsTags->Tags->foreignKey());
        $this->assertEquals('INNER', $this->PostsTags->Tags->joinType());
        $this->assertEquals('MeCms.Tags', $this->PostsTags->Tags->className());

        $this->assertTrue($this->PostsTags->hasBehavior('CounterCache'));
    }

    /**
     * Test for `validationDefault()` method
     * @test
     */
    public function testValidationDefault()
    {
        $this->assertEquals(
            'Cake\Validation\Validator',
            get_class($this->PostsTags->validationDefault(new \Cake\Validation\Validator))
        );
    }
}