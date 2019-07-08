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
 * @since       2.26.5
 */
namespace MeCms\ORM;

use Cake\ORM\Entity;
use Cake\View\HelperRegistry;
use Cake\View\View;

/**
 * Abstract class for `Post` and `Page` entity classes.
 *
 * This class provides some methods and properties common to both classes.
 */
abstract class PostAndPageEntity extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity()
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'preview' => false,
        'modified' => false,
    ];

    /**
     * Gets text as plain text (virtual field)
     * @return string|null
     */
    protected function _getPlainText()
    {
        if (empty($this->_properties['text'])) {
            return null;
        }

        //Loads the `BBCode` helper
        $BBCode = (new HelperRegistry(new View()))->load('MeTools.BBCode');

        return trim(strip_tags($BBCode->remove($this->_properties['text'])));
    }
}
