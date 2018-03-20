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
namespace MeCms\Model\Table;

use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\RulesChecker;
use MeCms\Model\Table\PostsAndPagesTables;

/**
 * Pages model
 * @property \Cake\ORM\Association\BelongsTo $Categories
 * @method \MeCms\Model\Entity\Page get($primaryKey, $options = [])
 * @method \MeCms\Model\Entity\Page newEntity($data = null, array $options = [])
 * @method \MeCms\Model\Entity\Page[] newEntities(array $data, array $options = [])
 * @method \MeCms\Model\Entity\Page|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \MeCms\Model\Entity\Page patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \MeCms\Model\Entity\Page[] patchEntities($entities, array $data, array $options = [])
 * @method \MeCms\Model\Entity\Page findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PagesTable extends PostsAndPagesTables
{
    use LocatorAwareTrait;

    /**
     * Name of the configuration to use for this table
     * @var string
     */
    public $cache = 'pages';

    /**
     * Returns a rules checker object that will be used for validating
     *  application integrity
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['category_id'], 'Categories', I18N_SELECT_VALID_OPTION));
        $rules->add($rules->isUnique(['slug'], I18N_VALUE_ALREADY_USED));
        $rules->add($rules->isUnique(['title'], I18N_VALUE_ALREADY_USED));

        return $rules;
    }

    /**
     * Initialize method
     * @param array $config The configuration for the table
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('pages');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Categories', ['className' => ME_CMS . '.PagesCategories'])
            ->setForeignKey('category_id')
            ->setJoinType('INNER')
            ->setTarget($this->getTableLocator()->get(ME_CMS . '.PagesCategories'))
            ->setAlias('Categories');

        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', ['Categories' => ['page_count']]);

        $this->_validatorClass = '\MeCms\Model\Validation\PageValidator';
    }
}
