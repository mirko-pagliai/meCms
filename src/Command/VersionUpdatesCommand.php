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
 * @since       2.26.1
 */
namespace MeCms\Command;

use Cake\Cache\Cache;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use MeTools\Console\Command;

/**
 * Performs some updates to the database or files needed for versioning
 */
class VersionUpdatesCommand extends Command
{
    /**
     * Hook method for defining this command's option parser
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        return $parser->setDescription(__d(
            'me_cms',
            'Performs some updates to the database or files needed for versioning'
        ));
    }

    /**
     * Adds the `enable_comments` field to `Pages` and `Posts` tables
     * @return void
     * @since 2.26.3
     */
    public function addEnableCommentsField()
    {
        Cache::clear(false, '_cake_model_');

        foreach (['Pages', 'Posts'] as $table) {
            $Table = $this->loadModel('MeCms.' . $table);

            if (!$Table->getSchema()->hasColumn('enable_comments')) {
                $Table->getConnection()->execute(sprintf(
                    'ALTER TABLE `%s` ADD `enable_comments` BOOLEAN NOT NULL DEFAULT TRUE AFTER `preview`',
                    $Table->getTable()
                ));
            }
        }
    }

    /**
     * Alter the length of the `tag` column of the `tags` table
     * @return void
     */
    public function alterTagColumnSize()
    {
        $Tags = $this->loadModel('MeCms.Tags');

        if ($Tags->getSchema()->getColumn('tag')['length'] < 255) {
            $Tags->getConnection()->execute('ALTER TABLE tags MODIFY tag varchar(255) NOT NULL');
        }
    }

    /**
     * Deletes old directories
     * @return void
     * @since 2.26.2
     */
    public function deleteOldDirectories()
    {
        rmdir_recursive(WWW_ROOT . 'fonts');
    }

    /**
     * Performs some updates to the database or files needed for versioning
     * @param \Cake\Console\Arguments $args The command arguments
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null The exit code or null for success
     * @uses addEnableCommentsField()
     * @uses alterTagColumnSize()
     * @uses deleteOldDirectories()
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->addEnableCommentsField();
        $this->alterTagColumnSize();
        $this->deleteOldDirectories();

        return null;
    }
}
