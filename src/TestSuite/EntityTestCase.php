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
 * @since       2.25.4
 */
namespace MeCms\TestSuite;

use MeTools\TestSuite\TestCase;
use MeTools\TestSuite\Traits\MockTrait;

/**
 * Abstract class for test entities
 */
abstract class EntityTestCase extends TestCase
{
    use MockTrait;

    /**
     * Entity instance
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $Entity;

    /**
     * Asserts that the entity has a "no accessible" property
     * @param string|array $property Property name
     * @return void
     * @uses $Entity
     */
    public function assertHasNoAccessibleProperty($property)
    {
        if (empty($this->Entity)) {
            $this->fail('The property `$this->Entity` has not been set');
        }

        foreach ((array)$property as $name) {
            $this->assertFalse($this->Entity->isAccessible($name));
        }
    }

    /**
     * Asserts that the entity has a virtual field
     * @param string|array $virtualField Virtual field name
     * @return void
     * @uses $Entity
     */
    public function assertHasVirtualField($virtualField)
    {
        if (empty($this->Entity)) {
            $this->fail('The property `$this->Entity` has not been set');
        }

        foreach ((array)$virtualField as $name) {
            $this->assertContains($name, $this->Entity->getVirtual());
        }
    }

    /**
     * Called before every test method
     * @return void
     * @uses $Entity
     */
    public function setUp()
    {
        parent::setUp();

        if (empty($this->Entity)) {
            $parts = explode('\\', get_class($this));
            array_splice($parts, 1, 2, []);
            $parts[count($parts) - 1] = substr($parts[count($parts) - 1], 0, -4);
            $className = implode('\\', $parts);

            $this->Entity = $this->getMockBuilder($className)
                ->setMethods(null)
                ->getMock();
        }
    }
}