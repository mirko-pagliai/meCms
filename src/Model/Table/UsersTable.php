<?php
/**
 * This file is part of MeCms.
 *
 * MeCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeCms.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeCms\Model\Table;

use Cake\Cache\Cache;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use MeCms\Model\Entity\User;
use MeCms\Model\Table\AppTable;

/**
 * Users model
 * @property \Cake\ORM\Association\BelongsTo $Groups
 * @property \Cake\ORM\Association\HasMany $Posts
 * @property \Cake\ORM\Association\HasMany $Tokens
 * @property \Cake\ORM\Association\HasMany $YoutubeVideos
 */
class UsersTable extends AppTable {
	/**
	 * Called after an entity has been deleted
	 * @param \Cake\Event\Event $event Event object
	 * @param \Cake\ORM\Entity $entity Entity object
	 * @param \ArrayObject $options Options
	 * @uses Cake\Cache\Cache::clear()
	 */
	public function afterDelete(\Cake\Event\Event $event, \Cake\ORM\Entity $entity, \ArrayObject $options) {
		Cache::clear(FALSE, 'users');		
	}
	
	/**
	 * Called after an entity is saved.
	 * @param \Cake\Event\Event $event Event object
	 * @param \Cake\ORM\Entity $entity Entity object
	 * @param \ArrayObject $options Options
	 * @uses Cake\Cache\Cache::clear()
	 */
	public function afterSave(\Cake\Event\Event $event, \Cake\ORM\Entity $entity, \ArrayObject $options) {
		Cache::clear(FALSE, 'users');
	}

    /**
     * Returns a rules checker object that will be used for validating application integrity
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules) {
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['group_id'], 'Groups'));
        return $rules;
    }
	
	/**
	 * "Active" find method
	 * @param Query $query Query object
	 * @param array $options Options
	 * @return Query Query object
	 */
	public function findActive(Query $query, array $options) {
        $query->where([
			sprintf('%s.active', $this->alias()) => TRUE,
			sprintf('%s.banned', $this->alias()) => FALSE
		]);
		
        return $query;
    }
	
	/**
	 * "Banned" find method
	 * @param Query $query Query object
	 * @param array $options Options
	 * @return Query Query object
	 */
	public function findBanned(Query $query, array $options) {
        $query->where([sprintf('%s.banned', $this->alias()) => TRUE]);
		
        return $query;
    }
	
	/**
	 * "Pending" find method
	 * @param Query $query Query object
	 * @param array $options Options
	 * @return Query Query object
	 */
	public function findPending(Query $query, array $options) {
        $query->where([
			sprintf('%s.active', $this->alias()) => FALSE,
			sprintf('%s.banned', $this->alias()) => FALSE
		]);
		
        return $query;
    }
	
	/**
	 * Gets the active users list
	 * @return array List
	 */
	public function getActiveList() {
		return $this->find('list')
			->where(['active' => TRUE])
			->cache('active_users_list', 'users')
			->toArray();
	}
	
	/**
	 * Gets the users list
	 * @return array List
	 */
	public function getList() {
		return $this->find('list')
			->cache('users_list', 'users')
			->toArray();
	}
	
    /**
     * Initialize method
     * @param array $config The configuration for the table
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('users');
        $this->displayField('full_name');
        $this->primaryKey('id');
		
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
            'joinType' => 'INNER',
            'className' => 'MeCms.UsersGroups'
        ]);
        $this->hasMany('Posts', [
            'foreignKey' => 'user_id',
            'className' => 'MeCms.Posts'
        ]);

        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', ['Groups' => ['user_count']]);
    }
	
	/**
	 * Build query from filter data
	 * @param \Cake\ORM\Query $query Query object
	 * @param array $data Filter data ($this->request->query)
	 * @return \Cake\ORM\Query $query Query object
	 * @uses \MeCms\Model\Table\AppTable::queryFromFilter()
	 */
	public function queryFromFilter($query, array $data = []) {
		$query = parent::queryFromFilter($query, $data);
		
		//"Username" field
		if(!empty($data['username']) && strlen($data['username']) > 2)
			$query->where([sprintf('%s.username LIKE', $this->alias()) => sprintf('%%%s%%', $data['username'])]);
		
		//"Group" field
		if(!empty($data['group']) && preg_match('/^[1-9]\d*$/', $data['group']))
			$query->where([sprintf('%s.group_id', $this->alias()) => $data['group']]);
		
		//"Status" field
		if(!empty($data['status']) && in_array($data['status'], ['active', 'pending', 'banned']))
			switch($data['status']) {
				case 'active':
					$query->where([
						sprintf('%s.active', $this->alias()) => TRUE,
						sprintf('%s.banned', $this->alias()) => FALSE
					]);
					break;
				case 'pending':
					$query->where([sprintf('%s.active', $this->alias()) => FALSE]);					
					break;
				case 'banned':
					$query->where([sprintf('%s.banned', $this->alias()) => TRUE]);		
					break;
			}
		
		return $query;
	}

    /**
     * Default validation rules
     * @param \Cake\Validation\Validator $validator Validator instance
	 * @return \MeCms\Model\Validation\UserValidator
	 */
    public function validationDefault(\Cake\Validation\Validator $validator) {
		return new \MeCms\Model\Validation\UserValidator;
    }
	
	/**
	 * Validation "not unique"
     * @param \Cake\Validation\Validator $validator Validator instance
	 * @return \MeCms\Model\Validation\UserValidator
	 * @see MeCms\Controller\UsersController::forgot_password()
	 */
	public function validationNotUnique(\Cake\Validation\Validator $validator) {
		$validator = new \MeCms\Model\Validation\UserValidator;
		
		//Username and email don't have to be unique 
		$validator->remove('username', 'unique')->remove('email', 'unique');
		
		//No field is required
		foreach($validator->getIterator() as $field => $value)
			$validator->requirePresence($field, FALSE);
		
		return $validator;
	}
	
	/**
	 * Validation "empty password"
     * @param \Cake\Validation\Validator $validator Validator instance
	 * @return \MeCms\Model\Validation\UserValidator
	 * @see MeCms\Controller\Admin\UsersController::edit()
	 */
	public function validationEmptyPassword(\Cake\Validation\Validator $validator) {
		$validator = new \MeCms\Model\Validation\UserValidator;
		
		//Allow empty passwords
		$validator->allowEmpty('password');
		$validator->allowEmpty('password_repeat');
		
		return $validator;
	}
}