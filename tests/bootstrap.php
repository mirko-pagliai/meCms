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

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Mailer\TransportFactory;
use EntityFileLog\Log\Engine\EntityFileLog;

ini_set('intl.default_locale', 'en_US');
date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// Path constants to a few helpful things.
define('ROOT', dirname(__DIR__) . DS);
define('VENDOR', ROOT . 'vendor' . DS);
define('CORE_PATH', VENDOR . 'cakephp' . DS . 'cakephp' . DS);
define('CAKE', CORE_PATH . 'src' . DS);
define('TESTS', ROOT . 'tests' . DS);
define('TEST_APP', TESTS . 'test_app' . DS);
define('APP', TEST_APP . 'TestApp' . DS);
define('APP_DIR', 'TestApp');
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', APP . 'webroot' . DS);
define('TMP', sys_get_temp_dir() . DS . 'me_cms' . DS);
define('CONFIG', APP . 'config' . DS);
define('CACHE', TMP . 'cache' . DS);
define('LOGS', TMP . 'log' . DS);
define('SESSIONS', TMP . 'sessions' . DS);
define('UPLOADED', WWW_ROOT . 'files' . DS);
define('LOGIN_RECORDS', TMP . 'login' . DS);

@mkdir(TMP);
@mkdir(LOGS);
@mkdir(SESSIONS);
@mkdir(CACHE);
@mkdir(CACHE . 'models');
@mkdir(CACHE . 'persistent');
@mkdir(CACHE . 'views');

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once CORE_PATH . 'config' . DS . 'bootstrap.php';
require_once ROOT . 'config' . DS . 'constants.php';

Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'App',
    'encoding' => 'UTF-8',
    'base' => false,
    'baseUrl' => false,
    'dir' => APP_DIR,
    'webroot' => 'webroot',
    'wwwRoot' => WWW_ROOT,
    'fullBaseUrl' => 'http://localhost',
    'imageBaseUrl' => 'img/',
    'jsBaseUrl' => 'js/',
    'cssBaseUrl' => 'css/',
    'paths' => [
        'plugins' => [APP . 'Plugin' . DS],
        'templates' => [
            APP . 'Template' . DS,
            ROOT . 'src' . DS . 'Template' . DS,
        ],
    ],
]);
Configure::write('Session', ['defaults' => 'php']);

Cache::setConfig([
    '_cake_core_' => [
        'engine' => 'File',
        'prefix' => 'cake_core_',
        'serialize' => true,
    ],
    '_cake_model_' => [
        'engine' => 'File',
        'prefix' => 'cake_model_',
        'serialize' => true,
    ],
    'default' => [
        'engine' => 'File',
        'prefix' => 'default_',
        'serialize' => true,
    ],
]);

// Ensure default test connection is defined
if (!getenv('db_dsn')) {
    putenv('db_dsn=mysql://travis@localhost/test');
}
ConnectionManager::setConfig('test', ['url' => getenv('db_dsn')]);

//This adds `apache_get_modules()` and `apache_get_version()` functions
require_once VENDOR . 'mirko-pagliai' . DS . 'php-tools' . DS . 'tests' . DS . 'apache_functions.php';

Configure::write('DatabaseBackup', ['connection' => 'test', 'target' => TMP . 'backups']);
Configure::write('Thumber', ['driver' => 'gd']);
Configure::write('Tokens.usersClassOptions', ['foreignKey' => 'user_id', 'className' => 'Users']);

//Sets debug and serialized logs
Log::setConfig('debug', [
    'className' => 'File',
    'path' => LOGS,
    'levels' => ['notice', 'info', 'debug'],
    'file' => 'debug',
]);
Log::setConfig('error', [
    'className' => EntityFileLog::class,
    'path' => LOGS,
    'file' => 'error',
    'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
]);

TransportFactory::setConfig('debug', ['className' => 'Debug']);
Email::setConfig('default', ['transport' => 'debug', 'log' => true]);

/**
 * This makes it believe that KCFinder is installed
 * @param bool $htaccess If `true`, it also creates the `.htaccess` file
 * @return void
 */
function create_kcfinder_files($htaccess = true)
{
    @create_file(KCFINDER . 'browse.php', '@version 3.12');
    $htaccess ? @create_file(KCFINDER . '.htaccess') : null;
}

Configure::write('Assets.target', TMP . 'assets');
Configure::write('pluginsToLoad', ['MeTools', 'MeCms']);

$_SERVER['PHP_SELF'] = '/';

if (!class_exists('PHPUnit\Runner\Version')) {
    class_alias('PHPUnit_Framework_MockObject_MockObject', 'PHPUnit\Framework\MockObject\MockObject');
}
