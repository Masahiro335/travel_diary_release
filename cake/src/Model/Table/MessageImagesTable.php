<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Time;
use Cake\I18n\FrozenTime;

/**
 * MessageImages Model
 *
 * @property \App\Model\Table\MessagesTable&\Cake\ORM\Association\BelongsTo $Messages
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\MessageImage newEmptyEntity()
 * @method \App\Model\Entity\MessageImage newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\MessageImage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MessageImage get($primaryKey, $options = [])
 * @method \App\Model\Entity\MessageImage findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\MessageImage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MessageImage[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\MessageImage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MessageImage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MessageImage[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\MessageImage[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\MessageImage[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\MessageImage[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MessageImagesTable extends Table
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

        $this->setTable('message_images');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Messages', [
            'foreignKey' => 'message_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'conditions' => [ 
                'Users.status_id IN' => \App\Model\Table\UserStatusesTable::USERS_DISPLAY,
                'Users.is_deleted' => false,
            ],
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
            ->integer('sort')
            ->requirePresence('sort', 'create')
            ->notEmptyString('sort');

        $validator
            ->boolean('is_deleted')
            ->notEmptyString('is_deleted');

	    $validator
            ->dateTime('image_upload_datetime')
            ->allowEmptyString('image_upload_datetime');

		$validator
            ->scalar('image_extension')
            ->allowEmptyString('image_extension');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }

    /**
	 * ???????????????????????????
	 * @author matsubara
	 * @param array $images ?????????????????????
	 * @param Entity $message ???????????????
	 * @param Entity $Auth ?????????????????????
	 * @return  boolean
	 */
	public function imageUpload($images, $message, $Auth)
	{

		//????????????????????????
		$messagesImagesCount = $this->find()
			->where([
				'message_id' => $message->id,
				'is_deleted' => false,
			])
			->count();

		$connection = \Cake\Datasource\ConnectionManager::get('default');

		foreach($images as $image){
			$connection->begin();
			try{
				//?????????????????????????????????
				$entMessageImage = $this->newEntity([
					'message_id' => $message->id,
					'user_id' => $Auth->id,
					'sort' => ++$messagesImagesCount,
					'image_upload_datetime' => new FrozenTime(),
					'image_extension' => pathinfo($image->getClientMediaType())['basename'],
					'is_deleted' => false,
                ]);

				if($this->save($entMessageImage)){
					//???????????????
					if($entMessageImage->s3Upload( $image, $entMessageImage ) == false) throw new \Exception('error: ???????????????????????????????????????');
                }
            $connection->commit();
			}catch( \Exception $e ){
				$connection->rollback();
				return false;
			}
		}
	}	


	/**
	 * ???????????????
	 * @author matsubara
	 * @param Entity $message ???????????????
	 * @param array $delete_image_ids ??????????????????ID
	 * @param Entity $Auth ?????????????????????
	 * @return  boolean
	 */
	public function imageDelete($message, $delete_image_ids, $Auth)
	{

		$connection = \Cake\Datasource\ConnectionManager::get('default');
		try{
			foreach ($delete_image_ids as $delete_image_id) {
				$connection->begin();

				//?????????????????????
				$entMessageImage = $this->find()
					->where([
						'id' => $delete_image_id,
						'message_id' => $message->id,
						'user_id' => $Auth->id,
						'is_deleted' => false,
					])
					->first();
					;

				if(empty($entMessageImage)) throw new \Exception('error: ?????????????????????????????????');

				//????????????
				if($entMessageImage->s3Delete($entMessageImage) == false) throw new \Exception('error: ???????????????????????????????????????');

				$entMessageImage->is_deleted = true;
				if($this->save($entMessageImage) === false) throw new \Exception('error: ????????????????????????????????????????????????');

				//????????????sort?????????
				$this->updateAll(
					[
						'sort = sort - 1',
						'modified' => new FrozenTime(),
					],
					[
						'message_id' => $message->id,
						'is_deleted' => false,
						'user_id' => $Auth->id,
						'sort >' => $entMessageImage->sort,
					]
				);

				$connection->commit();
            }
		}catch( \Exception $e ){
			//echo $e->getMessage();
			$connection->rollback();
			return false;
		}
	}
}
