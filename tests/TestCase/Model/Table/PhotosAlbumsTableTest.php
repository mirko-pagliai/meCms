<?php
declare(strict_types=1);
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

use MeCms\Model\Validation\PhotosAlbumValidator;
use MeCms\TestSuite\TableTestCase;

/**
 * PhotosAlbumsTableTest class
 */
class PhotosAlbumsTableTest extends TableTestCase
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
        'plugin.MeCms.Photos',
        'plugin.MeCms.PhotosAlbums',
    ];

    /**
     * Called after every test method
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        @unlink_recursive(PHOTOS, 'empty');
    }

    /**
     * Test for `afterDelete()` method
     * @test
     */
    public function testAfterDelete()
    {
        $this->loadFixtures();
        $entity = $this->Table->newEntity(['title' => 'new album', 'slug' => 'new-album']);
        $this->assertNotEmpty($this->Table->save($entity));
        $this->assertFileExists($entity->path);
        $this->assertTrue($this->Table->delete($entity));
        $this->assertFileNotExists($entity->path);
    }

    /**
     * Test for `afterSave()` method
     * @test
     */
    public function testAfterSave()
    {
        $this->loadFixtures();
        $entity = $this->Table->newEntity(['title' => 'new album', 'slug' => 'new-album']);
        $this->assertNotEmpty($this->Table->save($entity));
        $this->assertFileExists($entity->path);
        $this->assertFilePerms([0755, 0775, 0777], $entity->path);
    }

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
    }

    /**
     * Test for `initialize()` method
     * @test
     */
    public function testInitialize()
    {
        $this->assertEquals('photos_albums', $this->Table->getTable());
        $this->assertEquals('title', $this->Table->getDisplayField());
        $this->assertEquals('id', $this->Table->getPrimaryKey());

        $this->assertHasMany($this->Table->Photos);
        $this->assertEquals('album_id', $this->Table->Photos->getForeignKey());
        $this->assertEquals('MeCms.Photos', $this->Table->Photos->getClassName());

        $this->assertHasBehavior('Timestamp');

        $this->assertInstanceOf(PhotosAlbumValidator::class, $this->Table->getValidator());
    }

    /**
     * Test for `findActive()` method
     * @test
     */
    public function testFindActive()
    {
        $this->loadFixtures();
        $query = $this->Table->find('active');
        $this->assertStringEndsWith('FROM photos_albums PhotosAlbums INNER JOIN photos Photos ON (Photos.active = :c0 AND PhotosAlbums.id = (Photos.album_id))', $query->sql());
        $this->assertTrue($query->getValueBinder()->bindings()[':c0']['value']);
        $this->assertNotEmpty($query->count());
        array_map([$this, 'assertTrue'], $query->all()->extract('_matchingData.Photos.active')->toArray());
    }
}
