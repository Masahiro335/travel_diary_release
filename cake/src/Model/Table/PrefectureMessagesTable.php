<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Time;

/**
 * PrefectureMessages Model
 *
 * @property \App\Model\Table\MessagesTable&\Cake\ORM\Association\BelongsTo $Messages
 * @property \App\Model\Table\PrefecturesTable&\Cake\ORM\Association\BelongsTo $Prefectures
 *
 * @method \App\Model\Entity\PrefectureMessage newEmptyEntity()
 * @method \App\Model\Entity\PrefectureMessage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\PrefectureMessage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PrefectureMessage get($primaryKey, $options = [])
 * @method \App\Model\Entity\PrefectureMessage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\PrefectureMessage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PrefectureMessage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PrefectureMessage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PrefectureMessage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PrefectureMessage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\PrefectureMessage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\PrefectureMessage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\PrefectureMessage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PrefectureMessagesTable extends Table
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

        $this->setTable('prefecture_messages');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Messages', [
            'foreignKey' => 'message_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Prefectures', [
            'foreignKey' => 'prefecture_id',
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
        $rules->add($rules->existsIn(['message_id'], 'Messages'), ['errorField' => 'message_id']);
        $rules->add($rules->existsIn(['prefecture_id'], 'Prefectures'), ['errorField' => 'prefecture_id']);

        return $rules;
    }

	 /**
	 * ユーザーが行った旅行先
	 * @author matsubara
	 * Ajaxに出力
	 * @return  array ユーザーが行った旅行先
	 */
    public function userPrefectures($user_id = null)
	{
        if(empty($user_id)) return'';

		$userMessagesId = \Cake\ORM\TableRegistry::getTableLocator()->get('Messages')->find()
			->select('id')
			->where([
				'user_id' => $user_id,
				'is_deleted' => false,
			]);
		$queryUserPrefectures = $this->find()->where(['message_id IN' => $userMessagesId]);
		$arrayPrefectureIds = [];
		if(empty($queryUserPrefectures) == false){
			foreach($queryUserPrefectures as $userPrefecture) $arrayPrefectureIds[] = $userPrefecture->prefecture_id;
		}

        return $arrayPrefectureIds;
	}

	 /**
	 * 都道府県の投稿数ランキング
     * 1ヶ月ごとに更新
	 * @author matsubara
	 * @return query 都道府県の投稿数
	 */
    public function rankPrefectureMessages(){
        $query = $this->find()
            ->where([
                'PrefectureMessages.created >=' => date('Y-m-d', strtotime('-1 month')). ' 00:00:00',
                'PrefectureMessages.created <=' => date('Y-m-d').' 23:59:59.999999',
            ]);

        return $query
            ->contain(['Prefectures'])
            ->select([
                'name' => 'Prefectures.name',
                'count' => 'COUNT(PrefectureMessages.prefecture_id)',
                'rank' => 'RANK() OVER(ORDER BY COUNT(PrefectureMessages.prefecture_id) DESC)'
            ])
            ->group(['PrefectureMessages.prefecture_id','Prefectures.name'])
            ->order(['rank' => 'asc'])
            ;

    }
}
