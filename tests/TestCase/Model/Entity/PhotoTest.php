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
namespace MeCms\Test\TestCase\Model\Entity;

use Cake\ORM\Entity;
use MeCms\TestSuite\EntityTestCase;

/**
 * PhotoTest class
 */
class PhotoTest extends EntityTestCase
{
    /**
     * Called after every test method
     * @return void
     */
    public function tearDown()
    {
        safe_unlink_recursive(PHOTOS, 'empty');

        parent::tearDown();
    }

    /**
     * Test for fields that cannot be mass assigned using newEntity() or
     *  patchEntity()
     * @test
     */
    public function testNoAccessibleProperties()
    {
        $this->assertHasNoAccessibleProperty(['id', 'modified']);
    }

    /**
     * Test for `_getPath()` method
     * @test
     */
    public function testPathGetMutator()
    {
        $this->assertNull($this->Entity->path);

        $this->Entity->album_id = 1;
        $this->Entity->filename = 'photo.jpg';
        $this->assertEquals(PHOTOS . $this->Entity->album_id . DS . $this->Entity->filename, $this->Entity->path);
    }

    /**
     * Test for `_getPlainDescription()` method
     * @test
     */
    public function testPlainTextGetMutator()
    {
        $this->assertNull($this->Entity->plain_description);

        $expected = 'This is a text';

        $this->Entity->description = 'This is a [readmore /]text';
        $this->assertEquals($expected, $this->Entity->plain_description);
        $this->assertNotEquals($this->Entity->description, $this->Entity->plain_description);

        $this->Entity->description = $expected;
        $this->assertEquals($expected, $this->Entity->plain_description);
        $this->assertEquals($this->Entity->description, $this->Entity->plain_description);
    }

    /**
     * Test for `_getPreview()` method
     * @test
     */
    public function testPreviewGetMutator()
    {
        $this->assertNull($this->Entity->preview);

        $this->Entity->album_id = 1;
        $this->Entity->filename = 'photo1.jpg';
        file_put_contents(PHOTOS . $this->Entity->album_id . DS . $this->Entity->filename, $this->Entity->path, null);

        $this->assertInstanceof(Entity::class, $this->Entity->preview);
        $this->assertRegExp('/^http:\/\/localhost\/thumb\/[A-z0-9]+/', $this->Entity->preview->url);
        $this->assertEquals(null, $this->Entity->preview->width);
        $this->assertEquals(null, $this->Entity->preview->height);
    }

    /**
     * Test for virtual fields
     * @test
     */
    public function testVirtualFields()
    {
        $this->assertHasVirtualField(['path', 'plain_description', 'preview']);
    }
}
