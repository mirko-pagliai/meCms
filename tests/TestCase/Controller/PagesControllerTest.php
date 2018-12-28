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
use Cake\ORM\Entity;
use MeCms\Model\Entity\Page;
use MeCms\TestSuite\ControllerTestCase;

/**
 * PagesControllerTest class
 */
class PagesControllerTest extends ControllerTestCase
{
    /**
     * Cache keys to clear for each test
     * @var array
     */
    protected $cacheToClear = ['static_pages'];

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.MeCms.Pages',
        'plugin.MeCms.PagesCategories',
    ];

    /**
     * Tests for `view()` method
     * @test
     */
    public function testView()
    {
        $slug = $this->Table->find('active')->extract('slug')->first();

        $this->get(['_name' => 'page', $slug]);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Pages' . DS . 'view.ctp');
        $this->assertInstanceof(Page::class, $this->viewVariable('page'));

        $cache = Cache::read(sprintf('view_%s', md5($slug)), $this->Table->getCacheName());
        $this->assertEquals($this->viewVariable('page'), $cache->first());
    }

    /**
     * Tests for `view()` method, with a static page
     * @test
     */
    public function testViewWithStaticPage()
    {
        $this->get(['_name' => 'page', 'page-from-app']);
        $this->assertResponseOk();
        $this->assertResponseContains('This is a static page');
        $this->assertTemplate('StaticPages' . DS . 'page-from-app.ctp');

        $pageFromView = $this->viewVariable('page');
        $this->assertInstanceof(Entity::class, $pageFromView);
        $this->assertInstanceof(Entity::class, $pageFromView->category);
        $this->assertEquals([
            'category' => ['slug' => null, 'title' => null],
            'title' => 'Page From App',
            'subtitle' => null,
            'slug' => 'page-from-app',
        ], $pageFromView->toArray());
    }

    /**
     * Tests for `view()` method, with a static page from a plugin
     * @test
     */
    public function testViewWithStaticPageFromPlugin()
    {
        $this->loadPlugins(['TestPlugin']);
        $this->get(['_name' => 'page', 'test-from-plugin']);
        $this->assertResponseOk();
        $this->assertResponseContains('This is a static page from a plugin');
        $this->assertTemplate('Plugin' . DS . 'TestPlugin' . DS . 'src' . DS . 'Template' . DS . 'StaticPages' . DS . 'test-from-plugin.ctp');
    }

    /**
     * Tests for `preview()` method
     * @test
     */
    public function testPreview()
    {
        $this->setUserGroup('user');
        $slug = $this->Table->find('pending')->extract('slug')->first();

        $this->get(['_name' => 'pagesPreview', $slug]);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Pages' . DS . 'view.ctp');
        $this->assertInstanceof(Page::class, $this->viewVariable('page'));
    }
}
