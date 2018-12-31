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
namespace MeCms\Model\Validation;

use MeCms\Model\Validation\TagValidator;
use MeCms\Validation\AppValidator;

/**
 * Post validator class
 */
class PostValidator extends AppValidator
{
    /**
     * Construct.
     *
     * Adds some validation rules.
     * @uses MeCms\Validation\AppValidator::__construct()
     */
    public function __construct()
    {
        parent::__construct();

        //Category
        $this->add('category_id', [
            'naturalNumber' => [
                'message' => I18N_SELECT_VALID_OPTION,
                'rule' => 'naturalNumber',
            ],
        ])->requirePresence('category_id', 'create');

        //User (author)
        $this->requirePresence('user_id', 'create');

        //Title
        $this->requirePresence('title', 'create');

        //Slug
        $this->requirePresence('slug', 'create');

        //Text
        $this->requirePresence('text', 'create');

        //Tags
        $this->add('tags', [
            'validTags' => [
                'last' => true,
                'rule' => [$this, 'validTags'],
            ],
        ])->allowEmpty('tags');
    }

    /**
     * Tags validation method.
     *
     * It uses the `TagValidator` and checks its rules on each tag and returns
     *  `true` on success or a string with all a string with all errors found
     *  (separated by `PHP_EOL`) on failure.
     * @param string $value Field value
     * @return bool|string `true` on success or an error message on failure
     * @since 2.26.1
     * @uses \MeCms\Model\Validation\TagValidator
     */
    public function validTags($value)
    {
        $validator = new TagValidator;
        $messages = [];

        foreach ($value as $tag) {
            $errors = $validator->errors($tag);

            if (!empty($errors['tag'])) {
                foreach ($errors['tag'] as $error) {
                    $messages[] = __d('me_cms', 'Tag "{0}": {1}', $tag['tag'], lcfirst($error));
                }
            }
        }

        return empty($messages) ?: implode(PHP_EOL, $messages);
    }
}
