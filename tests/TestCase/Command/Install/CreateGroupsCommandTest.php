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
namespace MeCms\Test\TestCase\Command\Install;

use MeCms\TestSuite\TestCase;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;

/**
 * CreateGroupsCommandTest class
 */
class CreateGroupsCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.MeCms.Users',
        'plugin.MeCms.UsersGroups',
    ];

    /**
     * Test for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $UsersGroups = $this->getMockForModel('MeCms.UsersGroups', null);

        //A group already exists
        $this->exec('me_cms.create_groups -v');
        $this->assertExitWithSuccess();
        $this->assertOutputEmpty();
        $this->assertErrorContains('Some user groups already exist');

        //With no user groups
        $UsersGroups->deleteAll(['id >=' => '1']);
        $this->exec('me_cms.create_groups -v');
        $this->assertExitWithSuccess();
        $this->assertOutputContains('The user groups have been created');
        $this->assertErrorEmpty();

        //Checks the user groups exist
        $this->assertEquals([1, 2, 3], $UsersGroups->find()->extract('id')->toList());
    }
}
