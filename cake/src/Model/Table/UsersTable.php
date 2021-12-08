<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\Rule\IsUnique;
use Cake\I18n\FrozenTime;

/**
 * Users Model
 *
 * @property \App\Model\Table\UserStatusesTable&\Cake\ORM\Association\BelongsTo $UserStatuses
 * @property \App\Model\Table\PrefecturesTable&\Cake\ORM\Association\BelongsTo $Prefectures
 * @property \App\Model\Table\UniquesTable&\Cake\ORM\Association\BelongsTo $Uniques
 * @property \App\Model\Table\CommentsTable&\Cake\ORM\Association\HasMany $Comments
 * @property \App\Model\Table\FollowUsersTable&\Cake\ORM\Association\HasMany $FollowUsers
 * @property \App\Model\Table\GoodsTable&\Cake\ORM\Association\HasMany $Goods
 * @property \App\Model\Table\MessageImagesTable&\Cake\ORM\Association\HasMany $MessageImages
 * @property \App\Model\Table\MessagesTable&\Cake\ORM\Association\HasMany $Messages
 * @property \App\Model\Table\OperationLogsTable&\Cake\ORM\Association\HasMany $OperationLogs
 * @property \App\Model\Table\UserHomeImagesTable&\Cake\ORM\Association\HasMany $UserHomeImages
 *
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{
	//SNSログインの実行結果の定義
	const SNS_LOGIN_SUCCESS = 1;
	const SNS_LOGIN_ERROR = 2;
	const SNS_LOGIN_REDIRECT = 3;
	const SNS_LOGIN_UNKNOWN_TOKEN = 4;

	const ENCRYPT_KEY = "jy4bkek2yk2zehtm53p6anhp3j2hh7";
	const ENCRYPT_CIPHER = "aes-256-ecb";

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
		parent::initialize($config);

		$this->setTable('users');
		$this->setDisplayField('name');
		$this->setPrimaryKey('id');

		$this->addBehavior('Timestamp');

		$this->belongsTo('UserStatuses',[
			'foreignKey' => 'status_id'
		]);
		$this->belongsTo('Prefectures', [
			'foreignKey' => 'prefecture_id',
		]);
		$this->hasMany('Comments', [
			'foreignKey' => 'user_id',
			'conditions' => ['is_deleted' => false],
		]);
		$this->hasMany('Goods', [
			'foreignKey' => 'user_id',
			'conditions' => ['good' => true],
		]);
		$this->hasMany('MessageImages', [
			'foreignKey' => 'user_id',
		]);
		$this->hasMany('Messages', [
			'foreignKey' => 'user_id',
		]);
		$this->hasMany('OperationLogs', [
			'foreignKey' => 'user_id',
		]);
		$this->hasMany('FollowUsers', [
			'foreignKey' => 'user_id',
			'conditions' => 'FollowUsers.is_deleted = false'
		]);
		$this->hasMany('FollowerUsers', [
			'className' => 'FollowUsers',
			'foreignKey' => 'follow_user_id',
			'conditions' => 'FollowerUsers.is_deleted = false',
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
			->scalar('name')
			->requirePresence('name')
			->notEmptyString('name','名前は必須項目です。')
			->maxLength('name', 10, '10文字以下で入力してください。');

        $validator
			->scalar('email')
			->requirePresence('email')
			->notEmptyString('email','メールアドレスは必須項目です。')
			->email('email',false,'メールアドレスの形式ではありません。');

		$validator
			->scalar('password')
			->requirePresence('password')
			->notEmptyString('password','パスワードは必須項目です。')
			->add('password', 'password_valid', [
				'rule' => function( $password ){
					return (
						preg_match('/^[a-zA-Z0-9-]{8,}$/', $password) //8文字以上を強制 + 使用文字制限
						&& preg_match('/[a-zA-Z]/', $password) //アルファベットを1文字以上使用
						&& preg_match('/\d/', $password) //数字を1文字以上使用
					) ? true : false;
				},
				'message' => 'パスワードは半角英数字混合8文字以上を入力してください。'
			]);

		$validator
			->integer('status_id')
			->requirePresence('status_id')
			->notEmptyString('status_id','ステータスIDは必須項目です。');

		$validator
			->scalar('unique_id')
			->requirePresence('unique_id')
			->notEmptyString('unique_id','固有IDは必須項目です。');

			$validator
				->boolean('is_deleted')
				->notEmptyString('is_deleted');

        return $validator;
    }


	/**
	 * @author matsubara
	 * 仮登録の更新
	 */
	public function validationTmpUserUpdate(Validator $validator)
	{
		$validator
			->scalar('password')
			->requirePresence('password')
			->notEmptyString('password','パスワードは必須項目です。');

		$validator
			->integer('status_id')
			->requirePresence('status_id')
			->notEmptyString('status_id','ステータスIDは必須項目です。');

		return $validator;
	}


	/**
	 * @author matsubara
	 * プロフィールの更新
	 */
	public function validationProfileUpdate(Validator $validator)
	{
        $validator
			->scalar('name')
			->requirePresence('name')
			->notEmptyString('name','名前は必須項目です。')
			->maxLength('name', 10, '10文字以下で入力してください。');

		$validator
			->scalar('profile')
			->allowEmptyString('profile')
			->maxLength('profile', 140, '140文字以下で入力してください。');

		$validator
			->integer('prefecture_id')
			->allowEmptyString('prefecture_id');

		$validator
			->boolean('is_icon')
			->notEmptyString('is_icon');

		$validator
			->dateTime('icon_upload_datetime')
			->allowEmptyString('icon_upload_datetime');

		$validator
			->scalar('icon_extension')
			->allowEmptyString('icon_extension');

		$validator
			->boolean('is_home')
			->notEmptyString('is_home');

		$validator
			->dateTime('home_upload_datetime')
			->allowEmptyString('home_upload_datetime');

		$validator
			->scalar('home_extension')
			->allowEmptyString('home_extension');

		return $validator;
	}


	/**
	 * @author matsubara
	 * パスワード変更
	 */
	public function validationPasswordEdit(Validator $validator)
	{
		$validator
			->scalar('password')
			->requirePresence('password')
			->notEmptyString('password','パスワードは必須項目です。')
			->add('password', 'password_valid', [
				'rule' => function( $password ){
					return (
						preg_match('/^[a-zA-Z0-9-]{8,}$/', $password) //8文字以上を強制 + 使用文字制限
					) ? true : false;
				},
				'message' => 'パスワードは半角英数字の8文字以上を入力してください。'
			]);

		return $validator;
	}


	/**
	 * @author matsubara
	 * メールアドレスの変更
	 */
	public function validationEmailEdit(Validator $validator)
	{
        $validator
			->scalar('email')
			->requirePresence('email')
			->notEmptyString('email','メールアドレスは必須項目です。')
			->email('email',false,'メールアドレスの形式ではありません。');

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
        $rules->add($rules->existsIn(['status_id'], 'UserStatuses'), ['errorField' => 'status_id']);
        $rules->add($rules->existsIn(['prefecture_id'], 'Prefectures'), ['errorField' => 'prefecture_id']);
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }


    /**
    * ユーザーのランダムコードの作成
	* @author matsubara
	*
	* @param int ランダムコードの桁数
	*
	* @return string ランダムコード
	*/
    public function userUniqueCode($length = 8)
	{
		if ( $length <= 0)  return '';

		while(true){
			$bytes = random_bytes( $length );
			$rand_str = substr(bin2hex($bytes), 0, $length);
			
			if(!empty($rand_str)){
				$UniqueIdCount =  $this->find()
					->where([ 'unique_id' => $rand_str ])
					->count();

				if($UniqueIdCount == 0) break;
			}
		}

		return $rand_str;
    }


    /**
	* SNS会員ログイン
	*
	* ・Hybridauth の機能により、Selector::getUserProfile をコールした時にSNSのログイン認証画面にリダイレクトされる場合があります。
	* ・ユーザーのデータを取得するクエリに user_status_id の条件を指定していません。
	* 　パラメータ同様にデータ取得後利用側で user_status_id に対するバリデーションを行う設計です。
	*
	* @author matsubara
	*
	* @param int $sns_provider_id SNSトークンを取得するプロバイダーのID
	* @param string $redirectUrl SNSのログイン認証画面からリダイレクトするURL。主に呼び出し元のControllerのactionを再度実行するURLを指定します。
	*
	* @return Array ['result’=> int $this::SNS_LOGIN_～ のいずれかを返す, resultの値により以下のキーを返す、記述が無い場合はresultのみ]
	* 	SNS_LOGIN_SUCCESS: 'entUser' => Entity ユーザーデータ, 'snsProfile' => Object OAuthで取得したSNSのユーザーデータ
	* 	SNS_LOGIN_ERROR: 'message' => string エラーメッセージ
	* 	SNS_LOGIN_UNKNOWN_TOKEN: 'snsProfile' => Object OAuthで取得したSNSのユーザーデータ
	*/
	public function SnsLogin( $sns_provider_id, $redirectUrl ){
		$blnException = false;
		$this->SnsProviders = \Cake\ORM\TableRegistry::getTableLocator()->get('SnsProviders');
		$this->SnsTokens = \Cake\ORM\TableRegistry::getTableLocator()->get('SnsTokens');

		try{
			$snsProfile = $this->SnsProviders->OAuth($sns_provider_id, $redirectUrl);

			if( $snsProfile === false ) return ['result'=>$this::SNS_LOGIN_REDIRECT];

			//OAuth認証で取得したトークンとプロバイダーのIDでユーザーのIDを取得
			$entTokenToUserId = $this->SnsTokens->find()
				->where([
					'sns_provider_id' => $sns_provider_id,
					'sns_token' => $snsProfile->identifier,
				])
				->first()
			;

			//トークンからユーザーを取得できない場合、新規登録
			if( empty($entTokenToUserId) ){
				 return ['result'=>$this::SNS_LOGIN_UNKNOWN_TOKEN, 'snsProfile'=>$snsProfile];
			}

			$entUser = $this->find()
				->where(['id' => $entTokenToUserId->user_id])
				->first()
				;
			
			//ログイン実行
			if( empty($entUser) == false){
				return ['result'=>$this::SNS_LOGIN_SUCCESS, 'entUser'=>$entUser, 'snsProfile'=>$snsProfile];
			}

			return ['result'=>$this::SNS_LOGIN_UNKNOWN_TOKEN, 'message'=>'ログインに失敗しました。'];
		}catch( \App\Exception\SnsAuthException $Exception ){
			$blnException = true;
			return ['result'=>$this::SNS_LOGIN_UNKNOWN_TOKEN, 'message'=>$Exception->getMessage()];

		}catch( \Exception $Exception ){
			throw $Exception;
			$blnException = true;
			return ['result'=>$this::SNS_LOGIN_UNKNOWN_TOKEN, 'message'=>'ログインに失敗しました。'];

		}finally{
			if( $blnException ){
				$tblSnsProviders->RecentlyAdapterDisconnect();
			}
		}
	}


	/**
	 * ユーザーのデフォルトクエリ
	 * @param array $option
	 * @return  query
	 */
    public function queryDefalut($option = null)
    {
         $users = $this->find()
                ->contain([
                    'UserStatuses',
                    'Prefectures', 
					'Goods',
					'Comments',
                ])
				->where([
					'Users.status_id IN' => \App\Model\Table\UserStatusesTable::USERS_DISPLAY,
					'Users.is_deleted' => false,
				])
                ;
		return $users;
	}

	/**
	 * ユーザーの退会処理
	 * @param  entity $entUser 退会したユーザー
	 * @return  bool
	 */
    public function userWithdrawal($entUser = null)
    {
		//ユーザー画像削除
		if($entUser->is_icon == true){
			if($entUser->s3UserDelete($entUser, ICON)){
				$entUser->is_icon = false;
				$this->save($entUser);
			}
		}
		if($entUser->is_home == true){
			if($entUser->s3UserDelete($entUser, HOME)){
				$entUser->is_home = false;
				$this->save($entUser);
			}
		}
		//投稿画像削除
		$this->MessageImages = \Cake\ORM\TableRegistry::getTableLocator()->get('MessageImages');
		$queryMessageImages = $this->MessageImages
			->find()
			->where([
				'user_id' => $entUser->id,
				'is_deleted' => false
			]);
		foreach($queryMessageImages as $entMessageImage){
			@$entMessageImage->s3Delete($entMessageImage);
		}

		//「投稿画像」の論理削除
		$this->MessageImages->updateAll(
			['is_deleted' => true, 'modified' => new FrozenTime()],
			['user_id' => $entUser->id]
		);
		//「投稿」の論理削除
		\Cake\ORM\TableRegistry::getTableLocator()->get('Messages')->updateAll(
			['is_deleted' => true, 'modified' => new FrozenTime()],
			['user_id' => $entUser->id]
		);
		//「いいね」の論理削除
		\Cake\ORM\TableRegistry::getTableLocator()->get('Goods')->updateAll(
			['good' => false, 'modified' => new FrozenTime()],
			['user_id' => $entUser->id]
		);
		//「コメント」の論理削除
		\Cake\ORM\TableRegistry::getTableLocator()->get('Comments')->updateAll(
			['is_deleted' => true, 'modified' => new FrozenTime()],
			['user_id' => $entUser->id]
		);

		return true;
	}


	/**
	* OPenSSLでデータを暗号化
	* @author matsubara
	* @param int $id ユーザーID
	* @param string $email メールアドレス
	* @param string $token メールアドレスのトークン
	* @return string データを暗号化
	*/
	public function sslEncrypt( $id, $email, $token ){

		$url_array = [
			'id' => $id,
			'email' => $email,
			'token' => $token,
		];

		$url_sslEncrypted = openssl_encrypt( json_encode($url_array), $this::ENCRYPT_CIPHER, $this::ENCRYPT_KEY );
		return rawurlencode ($url_sslEncrypted);
	}


	/**
	* 暗号化されたデータの復元
	* @author matsubara
	* @param string $encrypted_string
	* @return array With id, email, token
	*/
	public function sslDecrypt( $encrypt_data ){

		$query_str = openssl_decrypt($encrypt_data, $this::ENCRYPT_CIPHER, $this::ENCRYPT_KEY);
		return json_decode( $query_str );
	}


	/**
	* 認証コードの作成　デフォルトは4桁
	* @author matsubara
	* @param int $length コードの桁数
	* @return string コード
	*/
	public function generateCode($length = 4)
	{
		$max = pow(10, $length) - 1;                    // コードの最大値算出
		$rand = random_int(0, $max);                    // 乱数生成
		$code = sprintf('%0'. $length. 'd', $rand);     // 乱数の頭0埋め

		return $code;
	}
	
}
