<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * OperationLogs Model
 *
 * @property \App\Model\Table\OperationTypesTable&\Cake\ORM\Association\BelongsTo $OperationTypes
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\OperationLog newEmptyEntity()
 * @method \App\Model\Entity\OperationLog newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\OperationLog[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\OperationLog get($primaryKey, $options = [])
 * @method \App\Model\Entity\OperationLog findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\OperationLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\OperationLog[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\OperationLog|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OperationLog saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OperationLog[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\OperationLog[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\OperationLog[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\OperationLog[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OperationLogsTable extends Table
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

        $this->setTable('operation_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('OperationTypes', [
            'foreignKey' => 'type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->scalar('ip_address')
            ->requirePresence('ip_address', 'create')
            ->notEmptyString('ip_address');

        $validator
            ->scalar('ua')
            ->requirePresence('ua', 'create')
            ->notEmptyString('ua');

        $validator
            ->scalar('cookie')
            ->requirePresence('cookie', 'create')
            ->notEmptyString('cookie');

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
        $rules->add($rules->existsIn(['type_id'], 'OperationTypes'), ['errorField' => 'type_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }


	/**
	* 操作ログを追加
	* @author matsubara
	* @param int $user_id
	* @param int $type_id
	* @param \Cake\Http\ServerRequest $request Controllerなどで作成されたリクエストオブジェクト
	*/
    public function addLog($user_id, $type_id, $request){
		try{
			$operationLogEntity = $this->newEmptyEntity();
			$operationLogEntity->type_id   			= $type_id;
			$operationLogEntity->user_id			= $user_id;
			$operationLogEntity->ip_address	        = $this->getIp( $request );
			$operationLogEntity->ua					= $request->getHeaderLine('User-Agent');
			$operationLogEntity->cookie				= $request->getCookie('PHPSESSID');
			$this->save($operationLogEntity);
		}catch( \Exception $e ){
			//例外発生時は特に何もしない
		}
	}


	/**
	* クライアントのIPを取得
	* - clientIp の代わりに使用、プロキシ経由元のIPを取得する場合に使用する。
	* - プロキシを経由していなければ clientIp の値を返す。
	* @author matsubara
	* @param \Cake\Http\ServerRequest $request Controllerなどで作成されたリクエストオブジェクト
	* @return string
	*/
	protected function getIp( $request ){
		$ret = $request->getHeaderLine('X-Forwarded-For');
		return empty($ret) ? $request->clientIp() : $ret;
	}
}
