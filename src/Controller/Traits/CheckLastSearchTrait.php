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
 * @since       2.16.1
 */
namespace MeCms\Controller\Traits;

/**
 * This trait provides a method to check if the latest search has been executed
 *  out of the minimum interval
 */
trait CheckLastSearchTrait
{
    /**
     * Checks if the latest search has been executed out of the minimum
     *  interval
     * @param string $id Query ID
     * @return bool
     */
    protected function checkLastSearch($id = false)
    {
        $interval = getConfig('security.search_interval');

        if (!$interval) {
            return true;
        }

        $id = $id ? md5($id) : false;
        $lastSearch = $this->request->getSession()->read('last_search');

        if ($lastSearch) {
            //Checks if it's the same search
            if ($id && !empty($lastSearch['id']) && $id === $lastSearch['id']) {
                return true;
            //Checks if the interval has not yet expired
            } elseif (($lastSearch['time'] + $interval) > time()) {
                return false;
            }
        }

        $this->request->getSession()->write('last_search', compact('id') + ['time' => time()]);

        return true;
    }
}
