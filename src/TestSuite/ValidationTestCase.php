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
 * @since       2.20.0
 */
namespace MeCms\TestSuite;

use Cake\Utility\Inflector;
use MeCms\TestSuite\TestCase;

/**
 * Abstract class for test validation classes
 */
abstract class ValidationTestCase extends TestCase
{
    /**
     * Table instance
     * @var \Cake\ORM\Table|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $Table;

    /**
     * If `true`, a mock instance of the table will be created
     * @var bool
     */
    protected $autoInitializeClass = true;

    /**
     * @var array
     */
    protected $example;

    /**
     * Test validation.
     * It tests the proper functioning of the example data.
     * @return void
     * @test
     */
    public function testAllDataAreRequired()
    {
        $this->assertAllDataAreRequired($this->example);
    }

    /**
     * Assert that all data are required.
     *
     * It first verifies that the entity, compiled with passed data, has no
     *  errors.
     *
     * Then it removes one field at a time, verifying that the "this field is
     *  required" error is generated.
     * @param array $data Valid data
     * @param array $exclude Key to be excluded
     * @return void
     * @uses $Table
     * @uses $example
     */
    public function assertAllDataAreRequired($data, $exclude = [])
    {
        $this->Table ?: $this->fail('The property `$this->Table` has not been set');
        $this->example ?: $this->fail('The property `$this->example` has not been set');

        $this->assertEmpty($this->Table->newEntity($this->example)->getErrors());

        foreach (array_keys($data) as $key) {
            if (in_array($key, $exclude)) {
                continue;
            }

            //Create a copy of the example data and removes the current value
            $copy = $data;
            unset($copy[$key]);

            $expectedErrors = [$key => ['_required' => 'This field is required']];
            $this->assertEquals($expectedErrors, $this->Table->newEntity($copy)->getErrors());
        }
    }

    /**
     * Called before every test method
     * @return void
     * @uses $Table
     * @uses $autoInitializeClass
     */
    public function setUp()
    {
        parent::setUp();

        if (!$this->Table && $this->autoInitializeClass) {
            $parts = explode('\\', get_class($this));
            $alias = Inflector::pluralize(substr(array_pop($parts), 0, -13));
            $className = sprintf('%s\\Model\Table\\%sTable', $parts[0], $alias);

            if (class_exists($className)) {
                $this->Table = $this->getMockForModel($alias, null, compact('className'));
            }
        }
    }
}
