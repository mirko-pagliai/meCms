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

use Cake\I18n\Time;
use MeCms\Model\Entity\PostsCategory;
use MeCms\Model\Validation\PostsCategoryValidator;
use MeCms\TestSuite\TableTestCase;

/**
 * PostsCategoriesTableTest class
 */
class PostsCategoriesTableTest extends TableTestCase
{
    /**
     * @var bool
     */
    public $autoFixtures = false;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms.Posts',
        'plugin.me_cms.PostsCategories',
    ];

    /**
     * Test for `buildRules()` method
     * @test
     */
    public function testBuildRules()
    {
        $this->loadFixtures();

        $example = ['title' => 'My title', 'slug' => 'my-slug'];

        $entity = $this->Table->newEntity($example);
        $this->assertNotEmpty($this->Table->save($entity));

        //Saves again the same entity
        $entity = $this->Table->newEntity($example);
        $this->assertFalse($this->Table->save($entity));
        $this->assertEquals([
            'slug' => ['_isUnique' => I18N_VALUE_ALREADY_USED],
            'title' => ['_isUnique' => I18N_VALUE_ALREADY_USED],
        ], $entity->getErrors());

        $entity = $this->Table->newEntity([
            'parent_id' => 999,
            'title' => 'My title 2',
            'slug' => 'my-slug-2',
        ]);
        $this->assertFalse($this->Table->save($entity));
        $this->assertEquals(['parent_id' => ['_existsIn' => I18N_SELECT_VALID_OPTION]], $entity->getErrors());
    }

    /**
     * Test for `initialize()` method
     * @test
     */
    public function testInitialize()
    {
        $this->assertEquals('posts_categories', $this->Table->getTable());
        $this->assertEquals('title', $this->Table->getDisplayField());
        $this->assertEquals('id', $this->Table->getPrimaryKey());

        $this->assertBelongsTo($this->Table->Parents);
        $this->assertEquals('parent_id', $this->Table->Parents->getForeignKey());
        $this->assertEquals(ME_CMS . '.PostsCategories', $this->Table->Parents->className());

        $this->assertHasMany($this->Table->Childs);
        $this->assertEquals('parent_id', $this->Table->Childs->getForeignKey());
        $this->assertEquals(ME_CMS . '.PostsCategories', $this->Table->Childs->className());

        $this->assertHasMany($this->Table->Posts);
        $this->assertEquals('category_id', $this->Table->Posts->getForeignKey());
        $this->assertEquals(ME_CMS . '.Posts', $this->Table->Posts->className());

        $this->assertHasBehavior(['Timestamp', 'Tree']);

        $this->assertInstanceOf(PostsCategoryValidator::class, $this->Table->getValidator());
    }
    /**
     * Test for the `belongsTo` association with `PostsCategories` parents
     * @test
     */
    public function testBelongsToParents()
    {
        $this->loadFixtures();

        $category = $this->Table->findById(4)->contain('Parents')->first();

        $this->assertNotEmpty($category->parent);
        $this->assertInstanceOf('MeCms\Model\Entity\PostsCategory', $category->parent);
        $this->assertEquals(3, $category->parent->id);

        $category = $this->Table->findById($category->parent->id)->contain('Parents')->first();

        $this->assertInstanceOf('MeCms\Model\Entity\PostsCategory', $category->parent);
        $this->assertEquals(1, $category->parent->id);
    }

    /**
     * Test for the `hasMany` association with `PostsCategories` childs
     * @test
     */
    public function testHasManyChilds()
    {
        $this->loadFixtures();

        $childs = $this->Table->find()->contain('Childs')->extract('childs')->first();
        $this->assertContainsInstanceOf(PostsCategory::class, $childs);

        foreach ($childs as $children) {
            $this->assertEquals(1, $children->parent_id);

            $childs = $this->Table->findById($children->id)->contain('Childs')->extract('childs')->first();
            $this->assertContainsInstanceOf(PostsCategory::class, $childs);

            foreach ($childs as $children) {
                $this->assertEquals(3, $children->parent_id);
            }
        }
    }

    /**
     * Test for `findActive()` method
     * @test
     */
    public function testFindActive()
    {
        $this->loadFixtures();

        $query = $this->Table->find('active');
        $this->assertStringEndsWith('FROM posts_categories PostsCategories INNER JOIN posts Posts ON (Posts.active = :c0 AND Posts.created <= :c1 AND PostsCategories.id = (Posts.category_id))', $query->sql());
        $this->assertTrue($query->getValueBinder()->bindings()[':c0']['value']);
        $this->assertInstanceOf(Time::class, $query->getValueBinder()->bindings()[':c1']['value']);
        $this->assertNotEmpty($query->count());

        foreach ($query as $entity) {
            $this->assertTrue($entity->_matchingData['Posts']->active &&
                !$entity->_matchingData['Posts']->created->isFuture());
        }
    }
}
