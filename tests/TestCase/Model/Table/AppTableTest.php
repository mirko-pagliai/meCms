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
namespace MeCms\Test\TestCase\Model\Table;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\Entity;
use MeCms\Model\Table\PhotosTable;
use MeCms\Model\Table\PostsCategoriesTable;
use MeCms\Model\Table\PostsTable;
use MeCms\TestSuite\TableTestCase;

/**
 * AppTableTest class
 */
class AppTableTest extends TableTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $Photos;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $Posts;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $PostsCategories;

    /**
     * If `true`, a mock instance of the table will be created
     * @var bool
     */
    protected $autoInitializeClass = false;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.MeCms.Photos',
        'plugin.MeCms.Posts',
        'plugin.MeCms.PostsCategories',
        'plugin.MeCms.PostsTags',
        'plugin.MeCms.Users',
    ];

    /**
     * Called before every test method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Photos = $this->getMockForModel('Photos', null, ['className' => PhotosTable::class]);
        $this->Posts = $this->getMockForModel('Posts', null, ['className' => PostsTable::class]);
        $this->PostsCategories = $this->getMockForModel('PostsCategories', null, ['className' => PostsCategoriesTable::class]);
    }

    /**
     * Test for `afterDelete()` method
     * @test
     */
    public function testAfterDelete()
    {
        Cache::write('testKey', 'testValue', $this->Posts->getCacheName());

        $this->Posts->afterDelete(new Event(null), new Entity, new ArrayObject);

        //The cache is cleared
        $this->assertFalse(Cache::read('testKey', $this->Posts->getCacheName()));
    }

    /**
     * Test for `afterSave()` method
     * @test
     */
    public function testAfterSave()
    {
        Cache::write('testKey', 'testValue', $this->Posts->getCacheName());

        $this->Posts->afterSave(new Event(null), new Entity, new ArrayObject);

        //The cache is cleared
        $this->assertFalse(Cache::read('testKey', $this->Posts->getCacheName()));
    }

    /**
     * Test for `beforeSave()` method
     * @test
     */
    public function testBeforeSave()
    {
        $example = [
            'user_id' => 1,
            'category_id' => 1,
            'title' => 'Example',
            'slug' => 'example',
            'text' => 'Example text',
        ];

        $entity = $this->Posts->save($this->Posts->newEntity($example));
        $this->assertNotEmpty($entity->created);
        $this->Posts->delete($entity);

        foreach ([null, ''] as $value) {
            $example['created'] = $value;
            $entity = $this->Posts->save($this->Posts->newEntity($example));
            $this->assertNotEmpty($entity->created);
            $this->Posts->delete($entity);
        }

        $example['created'] = $now = new Time;
        $entity = $this->Posts->save($this->Posts->newEntity($example));
        $this->assertEquals($now, $entity->created);
        $this->Posts->delete($entity);

        foreach (['2017-03-14 20:19', '2017-03-14 20:19:00'] as $value) {
            $example['created'] = $value;
            $entity = $this->Posts->save($this->Posts->newEntity($example));
            $this->assertEquals('2017-03-14 20:19:00', $entity->created->i18nFormat('yyyy-MM-dd HH:mm:ss'));
            $this->Posts->delete($entity);
        }

        //Now tries with a record that already exists
        $entity = $this->Posts->get(1);

        foreach ([null, ''] as $value) {
            $entity->created = $value;
            $entity = $this->Posts->save($entity);
            $this->assertNotEmpty($entity->created);
        }
    }

    /**
     * Test for `findActive()` method
     * @test
     */
    public function testFindActive()
    {
        $query = $this->Posts->find('active');
        $this->assertStringEndsWith('FROM posts Posts WHERE (Posts.active = :c0 AND Posts.created <= :c1)', $query->sql());
        $this->assertTrue($query->getValueBinder()->bindings()[':c0']['value']);
        $this->assertInstanceOf(Time::class, $query->getValueBinder()->bindings()[':c1']['value']);
        $this->assertNotEmpty($query->count());

        foreach ($query as $entity) {
            $this->assertTrue($entity->active && !$entity->created->isFuture());
        }
    }

    /**
     * Test for `findPending()` method
     * @test
     */
    public function testFindPending()
    {
        $query = $this->Posts->find('pending');
        $this->assertStringEndsWith('FROM posts Posts WHERE (Posts.active = :c0 OR Posts.created > :c1)', $query->sql());
        $this->assertFalse($query->getValueBinder()->bindings()[':c0']['value']);
        $this->assertInstanceOf(Time::class, $query->getValueBinder()->bindings()[':c1']['value']);

        foreach ($query as $entity) {
            $this->assertTrue(!$entity->active || $entity->created->isFuture());
        }
    }

    /**
     * Test for `findRandom()` method
     * @test
     */
    public function testFindRandom()
    {
        $query = $this->Posts->find('random');
        $this->assertStringEndsWith('FROM posts Posts ORDER BY rand() LIMIT 1', $query->sql());

        $query = $this->Posts->find('random')->limit(2);
        $this->assertStringEndsWith('FROM posts Posts ORDER BY rand() LIMIT 2', $query->sql());
    }

    /**
     * Test for `getCacheName()` method
     * @test
     */
    public function testGetCacheName()
    {
        $this->assertEquals('posts', $this->Posts->getCacheName());
        $this->assertEquals(['posts', 'users'], $this->Posts->getCacheName(true));
    }

    /**
     * Test for `getList()` method
     * @test
     */
    public function testGetList()
    {
        $query = $this->Photos->getList();
        $this->assertStringEndsWith('ORDER BY ' . $this->Photos->getDisplayField() . ' ASC', $query->sql());

        $expected = [
            1 => 'photo1.jpg',
            3 => 'photo3.jpg',
            4 => 'photo4.jpg',
            2 => 'photoa.jpg',
        ];
        $this->assertEquals($expected, $query->toArray());

        $fromCache = Cache::read('photos_list', $this->Photos->getCacheName())->toArray();
        $this->assertEquals($query->toArray(), $fromCache);
    }

    /**
     * Test for `getTreeList()` method
     * @test
     */
    public function testGetTreeList()
    {
        $query = $this->PostsCategories->getTreeList();
        $this->assertStringEndsNotWith('ORDER BY ' . $this->PostsCategories->getDisplayField() . ' ASC', $query->sql());

        $expected = [
            1 => 'First post category',
            3 => '—Sub post category',
            4 => '——Sub sub post category',
            2 => 'Another post category',
        ];
        $this->assertEquals($expected, $query->toArray());

        $fromCache = Cache::read('posts_categories_tree_list', $this->PostsCategories->getCacheName())->toArray();
        $this->assertEquals($query->toArray(), $fromCache);
    }

    /**
     * Test for `getTreeList()` method, with a model that does not have a tree
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage  Unknown finder method "treeList"
     */
    public function testGetTreeListModelDoesNotHaveTree()
    {
        $this->Posts->getTreeList();
    }

    /**
     * Test for `queryFromFilter()` method
     * @test
     */
    public function testQueryFromFilter()
    {
        $expectedSql = 'FROM posts Posts WHERE (Posts.id = :c0 AND Posts.title like :c1 AND Posts.user_id = :c2 AND Posts.category_id = :c3 AND Posts.active = :c4 AND Posts.priority = :c5 AND Posts.created >= :c6 AND Posts.created < :c7)';

        $data = [
            'id' => 2,
            'title' => 'Title',
            'user' => 3,
            'category' => 4,
            'active' => 'yes',
            'priority' => 3,
            'created' => '2016-12',
        ];

        $query = $this->Posts->queryFromFilter($this->Posts->find(), $data);
        $this->assertStringEndsWith($expectedSql, $query->sql());

        $params = collection($query->getValueBinder()->bindings())->extract('value')->map(function ($value) {
            if ($value instanceof Time) {
                return $value->i18nFormat('yyyy-MM-dd HH:mm:ss');
            }

            return $value;
        })->toList();

        $this->assertEquals([
            2,
            '%Title%',
            3,
            4,
            true,
            3,
            '2016-12-01 00:00:00',
            '2017-01-01 00:00:00',
        ], $params);

        $data['active'] = 'no';

        $query = $this->Posts->queryFromFilter($this->Posts->find(), $data);
        $this->assertStringEndsWith($expectedSql, $query->sql());
        $this->assertEquals(false, $query->getValueBinder()->bindings()[':c4']['value']);

        $data = ['filename' => 'image.jpg'];

        $query = $this->Photos->queryFromFilter($this->Photos->find(), $data);
        $this->assertStringEndsWith('FROM photos Photos WHERE Photos.filename like :c0', $query->sql());
        $this->assertEquals('%image.jpg%', $query->getValueBinder()->bindings()[':c0']['value']);
    }

    /**
     * Test for `queryFromFilter()` method, with invalid data
     * @test
     */
    public function testQueryFromFilterWithInvalidData()
    {
        $data = [
            'title' => 'ab',
            'priority' => 6,
            'created' => '2016-12-30',
        ];

        $query = $this->Posts->queryFromFilter($this->Posts->find(), $data);
        $this->assertEmpty($query->getValueBinder()->bindings());

        $data = ['filename' => 'ab'];

        $query = $this->Photos->queryFromFilter($this->Photos->find(), $data);
        $this->assertEmpty($query->getValueBinder()->bindings());
    }
}
