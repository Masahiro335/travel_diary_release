<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Messages Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CommentsTable&\Cake\ORM\Association\HasMany $Comments
 * @property \App\Model\Table\GoodsTable&\Cake\ORM\Association\HasMany $Goods
 * @property \App\Model\Table\MessageImagesTable&\Cake\ORM\Association\HasMany $MessageImages
 * @property \App\Model\Table\PrefectureMessagesTable&\Cake\ORM\Association\HasMany $PrefectureMessages
 *
 * @method \App\Model\Entity\Message newEmptyEntity()
 * @method \App\Model\Entity\Message newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Message[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Message get($primaryKey, $options = [])
 * @method \App\Model\Entity\Message findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Message patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Message[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Message|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Message saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Message[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Message[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Message[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Message[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MessagesTable extends Table
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

        $this->setTable('messages');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'conditions' => [ 
                'Users.status_id IN' => \App\Model\Table\UserStatusesTable::USERS_DISPLAY,
                'Users.is_deleted' => false,
            ],
        ]);
        $this->hasMany('Comments', [
            'foreignKey' => 'message_id',
        ])->setSort([
            'Comments.created' => 'DESC'
        ]);
        $this->hasMany('Goods', [
            'foreignKey' => 'message_id',
            'conditions' => ['good' => true],
		]);
		$this->hasMany('AuthGoods', [
			'className' => 'Goods',
            'foreignKey' => 'message_id'
		]);
        $this->hasMany('MessageImages', [
            'foreignKey' => 'message_id',
            'conditions' => ['is_deleted' => false],
        ])->setSort([
            'MessageImages.sort' => 'ASC'
        ]);
        $this->hasMany('PrefectureMessages', [
            'foreignKey' => 'message_id',
            'saveStrategy' => 'replace',
        ]);
        $this->belongsToMany('Prefectures', [
			'foreignKey' => 'message_id',
			'targetForeignKey' => 'prefecture_id',
			'joinTable' => 'prefecture_messages',
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
            ->scalar('message')
            ->requirePresence('message', 'create')
            ->notEmptyString('message','メッセージは入力必須です。')
            ->maxLength('message', 140, '140文字以下で入力してください。');

        $validator
            ->boolean('is_edit')
            ->notEmptyString('is_edit');

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
	 * 投稿のデフォルトクエリ
	 * @param array $option
	 * @return query
	 */
	public function queryDefalut($option = null)
	{
			$messages = $this->find()
				->contain([
					'Prefectures',
					'Users', 
					'MessageImages', 
					'Goods',
					'Comments.Users' => function($q){
						return $q->where([
							'Comments.user_id = Users.id',
							'Users.status_id IN' => \App\Model\Table\UserStatusesTable::USERS_DISPLAY,
							'Users.is_deleted' => false,
						]);
					}
				])
                ->where([
                    'Messages.is_deleted' => false,
                    'Users.is_deleted' => false,
                    'Users.status_id IN' => \App\Model\Table\UserStatusesTable::USERS_DISPLAY,
                ])
				->order([
					'Messages.created' => 'desc',
					'Messages.id' => 'asc'
				]);
				;


        //ログインユーザーの「いいね」を取得
		if(empty($option['AuthGoods']) == false){
            $Auth = $option['AuthGoods'];
			$messages->contain([
				'AuthGoods' => function($q) use ($Auth){
					return $q->where([
						'user_id' => $Auth['id'],
						'good' => true,
					]);
				}
			]);
		}

        //指定したユーザーのみの投稿
        if(empty($option['UsersId']) == false){
            $messages->where(['Messages.user_id' => $option['UsersId']]);
        }

        //ユーザーの「いいね」のみの投稿
        if(empty($option['GoodsMessages']) == false){
            $GoodsMessagesId = \Cake\ORM\TableRegistry::getTableLocator()->get('Goods')->find()
                ->SELECT('message_id')
                ->WHERE([
                    'user_id' => $option['GoodsMessages'],
                    'good' => true,
                ]);
            $messages->where(['Messages.id IN' => $GoodsMessagesId]);
        }

        //指定したユーザーの旅行先を表示
        if(empty($option['PrefectureSelect']) == false){
            $PrefectureSelectID = \Cake\ORM\TableRegistry::getTableLocator()->get('PrefectureMessages')->find()
                ->SELECT('message_id')
                ->WHERE(['prefecture_id' => $option['PrefectureSelect']]);
            $messages->where(['Messages.id IN' => $PrefectureSelectID]);
        }
    
		return $messages;
	}

    public function UsersMessages($user = null, $Auth = null)
	{
		if( empty($user) ) return'';

		$guery = $this->queryDefalut([
			'UsersId' => $user->id,
			'AuthGoods' => $Auth
		])
		;

		return $guery;
    }

    public function UsersGoodsMessages($user = null, $Auth = null)
    {
		if( empty($user) ) return'';

		$guery = $this->queryDefalut([
			'AuthGoods' => empty($Auth) ? '' : $Auth,
			'GoodsMessages' => $user->id,
		]);

		return $guery;
    }


	/**
	 * いいねのカウントをセット
	 * @author matsubara
	 * @param Query
	 * @return Query いいねのカウントをセットしたクエリ
	 */
	public function countGoods($query = null)
	{
		$query
			->select([
				'count_goods' => \Cake\ORM\TableRegistry::getTableLocator()->get('Goods')->find()
					->select(['cnt'=>'count(*)'])
					->where([
						'Goods.message_id = Messages.id',
						'Goods.good = true'
					])
			])
			->enableAutoFields(true);

		return $query;
	}

    /**
	 * コメントのカウントをセット
	 * @author matsubara
	 * @param Query
	 * @return Query コメントのカウントをセットしたクエリ
	 */
	public function countComments($query = null)
	{
		$query
			->select([
				'count_comments' => \Cake\ORM\TableRegistry::getTableLocator()->get('Comments')->find()
					->select(['cnt'=>'count(*)'])
					->where([
						'Comments.message_id = Messages.id',
						'Comments.is_deleted = false'
					])
			])
			->enableAutoFields(true);

		return $query;
	}

}
