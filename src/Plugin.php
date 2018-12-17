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
 * @since       2.24.0
 */
namespace MeCms;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use MeCms\Command\AddUserCommand;
use MeCms\Command\GroupsCommand;
use MeCms\Command\Install\CopyConfigCommand;
use MeCms\Command\Install\CreateAdminCommand;
use MeCms\Command\Install\CreateGroupsCommand;
use MeCms\Command\Install\FixKcfinderCommand;
use MeCms\Command\Install\RunAllCommand;
use MeCms\Command\UsersCommand;
use MeTools\Command\Install\CreateDirectoriesCommand;
use MeTools\Command\Install\CreateVendorsLinksCommand;
use MeTools\Command\Install\SetPermissionsCommand;

/**
 * Plugin class
 */
class Plugin extends BasePlugin
{
    /**
     * Load all the application configuration and bootstrap logic
     * @param PluginApplicationInterface $app The host application
     * @return void
     * @uses setVendorLinks()
     * @uses setWritableDirs()
     */
    public function bootstrap(PluginApplicationInterface $app)
    {
        $pluginsToLoad = [
            'Assets',
            'DatabaseBackup',
            'MeTools',
            'Recaptcha' => ['path' => ROOT . DS . 'vendor' . DS . 'crabstudio' . DS . 'recaptcha' . DS],
            'RecaptchaMailhide',
            'Thumber',
            'Tokens',
        ];

        foreach ($pluginsToLoad as $plugin => $config) {
            if (is_int($plugin) && !is_array($config)) {
                list($plugin, $config) = [$config, []];
            }

            $className = sprintf('%s\Plugin', $plugin);
            if (class_exists($className)) {
                $plugin = new $className;
                $plugin->bootstrap($app);
            }

            $app->addPlugin($plugin, $config);
        }

        parent::bootstrap($app);

        if (PHP_SAPI !== 'cli') {
            //Loads DebugKit, if debugging is enabled
            if (getConfig('debug') && !$app->getPlugins()->has('DebugKit')) {
                $app->addPlugin('DebugKit');
            }

            $app->addPlugin('Gourmet/CommonMark');
            $app->addPlugin('WyriHaximus/MinifyHtml');
        }

        $this->setVendorLinks();
        $this->setWritableDirs();
    }

    /**
     * Add console commands for the plugin
     * @param Cake\Console\CommandCollection $commands The command collection to update
     * @return Cake\Console\CommandCollection
     * @uses setVendorLinks()
     * @uses setWritableDirs()
     */
    public function console($commands)
    {
        $this->setVendorLinks();
        $this->setWritableDirs();

        $commands->add('me_cms.add_user', AddUserCommand::class);
        $commands->add('me_cms.groups', GroupsCommand::class);
        $commands->add('me_cms.users', UsersCommand::class);

        $commands->add('me_cms.copy_config', CopyConfigCommand::class);
        $commands->add('me_cms.create_admin', CreateAdminCommand::class);
        $commands->add('me_cms.create_groups', CreateGroupsCommand::class);
        $commands->add('me_cms.fix_kcfinder', FixKcfinderCommand::class);
        $commands->add('me_cms.install', RunAllCommand::class);

        //Commands from MeTools
        $commands->add('me_cms.create_directories', CreateDirectoriesCommand::class);
        $commands->add('me_cms.create_vendors_links', CreateVendorsLinksCommand::class);
        $commands->add('me_cms.set_permissions', SetPermissionsCommand::class);

        return $commands;
    }

    /**
     * Sets symbolic links for vendor assets to be created
     * @return array
     */
    protected function setVendorLinks()
    {
        $links = array_unique(array_merge(Configure::read('VENDOR_LINKS', []), [
            'npm-asset' . DS . 'js-cookie' . DS . 'src' => 'js-cookie',
            'sunhater' . DS . 'kcfinder' => 'kcfinder',
            'enyo' . DS . 'dropzone' . DS . 'dist' => 'dropzone',
        ]));

        return Configure::write('VENDOR_LINKS', $links) ? $links : false;
    }

    /**
     * Sets directories to be created and must be writable
     * @return array
     */
    protected function setWritableDirs()
    {
        $dirs = array_unique(array_merge(Configure::read('WRITABLE_DIRS', []), [
            getConfigOrFail('Assets.target'),
            getConfigOrFail('DatabaseBackup.target'),
            getConfigOrFail('Thumber.target'),
            BANNERS,
            LOGIN_RECORDS,
            PHOTOS,
            USER_PICTURES,
        ]));

        return Configure::write('WRITABLE_DIRS', $dirs) ? $dirs : false;
    }
}
