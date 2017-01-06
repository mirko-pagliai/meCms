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
namespace MeCms\Test\TestCase\Model\Validation;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * BannersPositionValidatorTest class
 */
class BannersPositionValidatorTest extends TestCase
{
    /**
     * @var \MeCms\Model\Table\BannersPositionsTable
     */
    protected $BannersPositions;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms.banners_positions',
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

        $this->BannersPositions = TableRegistry::get('MeCms.BannersPositions');
    }

    /**
     * Test validation for `title` property
     * @test
     */
    public function testValidatorForTitle()
    {
        $entity = $this->BannersPositions->newEntity(['title' => 'my-title']);
        $this->assertEmpty($entity->errors());

        $entity = $this->BannersPositions->newEntity([]);
        $this->assertEquals(['title' => ['_required' => 'This field is required']], $entity->errors());

        $entity = $this->BannersPositions->newEntity(['title' => 'ab']);
        $this->assertEquals([
            'title' => [
                'lengthBetween' => 'Must be between 3 and 100 chars',
                'slug' => 'Allowed chars: lowercase letters, numbers, dash',
            ],
        ], $entity->errors());

        $entity = $this->BannersPositions->newEntity(['title' => str_repeat('a', 101)]);
        $this->assertEquals(['title' => ['lengthBetween' => 'Must be between 3 and 100 chars']], $entity->errors());

        $entity = $this->BannersPositions->newEntity(['title' => 'abc']);
        $this->assertEmpty($entity->errors());

        $entity = $this->BannersPositions->newEntity(['title' => str_repeat('a', 100)]);
        $this->assertEmpty($entity->errors());
    }
}
