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
namespace MeCms\Test\TestCase\Form;

use Cake\Network\Exception\InternalErrorException;
use DatabaseBackup\Utility\BackupExport;
use MeCms\Form\BackupForm;
use MeTools\TestSuite\TestCase;

/**
 * BackupFormTest class
 */
class BackupFormTest extends TestCase
{
    /**
     * @var \DatabaseBackup\Utility\BackupExport
     */
    public $BackupExport;

    /**
     * @var \MeCms\Form\BackupForm
     */
    public $BackupForm;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->BackupExport = $this->getMockBuilder(get_class(new BackupExport))
            ->setMethods(['export', 'filename'])
            ->getMock();

        $this->BackupExport->method('filename')
            ->will($this->returnSelf());

        $this->BackupForm = new BackupForm;
    }

    /**
     * Test validation.
     * It tests the proper functioning of the example data.
     * @test
     */
    public function testValidationExampleData()
    {
        $this->assertTrue($this->BackupForm->validate(['filename' => 'file.sql']));
        $this->assertEmpty($this->BackupForm->errors());

        $this->assertFalse($this->BackupForm->validate([]));
        $errors = $this->BackupForm->errors();
        $this->assertEquals(['filename' => ['_required' => 'This field is required']], $errors);
    }

    /**
     * Test validation for `filename` property
     * @test
     */
    public function testValidationForFilename()
    {
        foreach ([
            'file',
            'file.sql.',
            'file.bz2',
            'file.gz',
            '.sql',
            'file.gif',
        ] as $value) {
            $this->assertFalse($this->BackupForm->validate(['filename' => $value]));
            $errors = $this->BackupForm->errors();
            $this->assertEquals(['filename' => ['extension' => 'Valid extensions: sql, sql.gz, sql.bz2']], $errors);
        }

        foreach (['file.sql', 'file.sql.bz2', 'file.sql.gz'] as $value) {
            $this->assertTrue($this->BackupForm->validate(['filename' => $value]));
            $this->assertEmpty($this->BackupForm->errors());
        }

        $this->assertFalse($this->BackupForm->validate(['filename' => str_repeat('a', 252) . '.sql']));
        $errors = $this->BackupForm->errors();
        $this->assertEquals(['filename' => ['maxLength' => 'Must be at most 255 chars']], $errors);

        $this->assertTrue($this->BackupForm->validate(['filename' => str_repeat('a', 251) . '.sql']));
        $this->assertEmpty($this->BackupForm->errors());
    }

    /**
     * Tests for `getBackupExportInstance()` method
     * @test
     */
    public function testGetBackupExportInstance()
    {
        $this->assertEmpty($this->getProperty($this->BackupForm, 'BackupExport'));

        $instance = $this->invokeMethod($this->BackupForm, 'getBackupExportInstance');
        $this->assertInstanceOf('DatabaseBackup\Utility\BackupExport', $instance);

        $this->assertEquals($instance, $this->getProperty($this->BackupForm, 'BackupExport'));
    }

    /**
     * Tests for `_execute()` method
     * @test
     */
    public function testExecute()
    {
        $this->BackupForm = $this->getMockBuilder(get_class($this->BackupForm))
            ->setMethods(['getBackupExportInstance'])
            ->getMock();

        $this->BackupForm->expects($this->atLeastOnce())
            ->method('getBackupExportInstance')
            ->will($this->returnCallback(function () {
                $this->BackupExport->method('export')
                    ->will($this->returnValue(true));

                return $this->BackupExport;
            }));

        $this->assertTrue($this->BackupForm->execute(['filename' => 'test.sql']));

        $this->BackupForm->expects($this->atLeastOnce())
            ->method('getBackupExportInstance')
            ->will($this->throwException(new InternalErrorException));

        $this->assertFalse($this->BackupForm->execute(['filename' => 'test.sql']));
    }
}
