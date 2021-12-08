<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FollowUsers Model
 *
 * @property \App\Model\Table\FollowUsersTable&\Cake\ORM\Association\BelongsTo $FollowUsers
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\FollowUsersTable&\Cake\ORM\Association\HasMany $FollowUsers
 *
 * @method \App\Model\Entity\FollowUser newEmptyEntity()
 * @method \App\Model\Entity\FollowUser newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\FollowUser[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FollowUser get($primaryKey, $options = [])
 * @method \App\Model\Entity\FollowUser findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\FollowUser patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FollowUser[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FollowUser|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FollowUser saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FollowUser[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FollowUser[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\FollowUser[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FollowUser[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FollowUsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('follow_users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('FollowUsers', [
            'foreignKey' => 'follow_user_id',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'conditions' => [ 
                'Users.status_id IN' => \App\Model\Table\UserStatusesTable::USERS_DISPLAY,
                'Users.is_deleted' => false,
            ],
        ]);
        $this->belongsTo('FollowerUsers', [
            'foreignKey' => 'follow_user_id',
            'joinType' => 'INNER',
        ]);
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->boolean('is_deleted')
            ->notEmptyString('is_deleted');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }

    /**
	 * ユーザーがフォローしたユーザー
	 * @param array entity $user
	 * @return query
	 */
	public function followUser($Auth = null, $user = null){
        if(empty($Auth) || empty($user)) return '';

        $followUser = $this->find()
            ->where([
                'user_id' => $Auth->id,
                'follow_user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->first()
            ;

        return $followUser;

    }
}
