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
namespace MeCms\Test\TestCase\Controller;

use Cake\Cache\Cache;
use MeCms\Model\Entity\Post;
use MeCms\Model\Entity\PostsCategory;
use MeCms\TestSuite\ControllerTestCase;

/**
 * PostsCategoriesControllerTest class
 */
class PostsCategoriesControllerTest extends ControllerTestCase
{
    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.MeCms.Posts',
        'plugin.MeCms.PostsCategories',
        'plugin.MeCms.PostsTags',
        'plugin.MeCms.Tags',
        'plugin.MeCms.Users',
    ];

    /**
     * Tests for `index()` method
     * @test
     */
    public function testIndex()
    {
        $this->get(['_name' => 'postsCategories']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('PostsCategories' . DS . 'index.ctp');
        $this->assertContainsOnlyInstancesOf(PostsCategory::class, $this->viewVariable('categories'));

        $cache = Cache::read('categories_index', $this->Table->getCacheName());
        $this->assertEquals($this->viewVariable('categories')->toArray(), $cache->toArray());
    }

    /**
     * Tests for `view()` method
     * @test
     */
    public function testView()
    {
        $slug = $this->Table->find('active')->extract('slug')->first();
        $url = ['_name' => 'postsCategory', $slug];

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('PostsCategories' . DS . 'view.ctp');
        $this->assertInstanceof(PostsCategory::class, $this->viewVariable('category'));
        $this->assertContainsOnlyInstancesOf(Post::class, $this->viewVariable('posts'));

        $cache = sprintf('category_%s_limit_%s_page_%s', md5($slug), getConfigOrFail('default.records'), 1);
        list($postsFromCache, $pagingFromCache) = array_values(Cache::readMany(
            [$cache, sprintf('%s_paging', $cache)],
            $this->Table->getCacheName()
        ));
        $this->assertEquals($this->viewVariable('posts')->toArray(), $postsFromCache->toArray());
        $this->assertNotEmpty($pagingFromCache['Posts']);

        //GET request again. Now the data is in cache
        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertNotEmpty($this->_controller->request->getParam('paging')['Posts']);

        //GET request with query string
        $this->get($url + ['?' => ['q' => $slug]]);
        $this->assertRedirect($url);

        //GET request with a no existing category
        $this->get(['_name' => 'postsCategory', 'no-existing']);
        $this->assertResponseError();
    }
}
