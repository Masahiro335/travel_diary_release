<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserStatuses Model
 *
 * @method \App\Model\Entity\UserStatus newEmptyEntity()
 * @method \App\Model\Entity\UserStatus newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\UserStatus[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserStatus get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserStatus findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\UserStatus patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserStatus[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserStatus|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserStatus saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserStatus[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserStatus[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserStatus[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserStatus[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserStatusesTable extends Table
{
    //登録状態のIDに関する定義
	const ID = [
		'TEMPORARY' => 10,
		'NORMAL' => 20,
		'RESTRICTION' => 30,
        'WITHDRAWAL' => 40,
		'FORCED_WITHDRAWAL' => 50,
	];

    //フロントに表示できるユーザー
	const USERS_DISPLAY = [10, 20, 40];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('user_statuses');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
