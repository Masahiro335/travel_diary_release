<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SnsProviders Model
 *
 * @property \App\Model\Table\SnsTokensTable&\Cake\ORM\Association\HasMany $SnsTokens
 *
 * @method \App\Model\Entity\SnsProvider newEmptyEntity()
 * @method \App\Model\Entity\SnsProvider newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SnsProvider[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SnsProvider get($primaryKey, $options = [])
 * @method \App\Model\Entity\SnsProvider findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SnsProvider patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SnsProvider[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SnsProvider|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SnsProvider saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SnsProvider[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SnsProvider[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SnsProvider[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SnsProvider[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SnsProvidersTable extends Table
{

    const SNS_ID = [
        'GOOGLE' => 10,
        'TWETTER' => 20,
		'LINE' => 30,
	];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('sns_providers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('SnsTokens', [
            'foreignKey' => 'sns_provider_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->boolean('is_deleted')
            ->notEmptyString('is_deleted');

        return $validator;
    }
    
    
    /**
	* OAuth認証を行い、SNSのユーザー情報を取得する
	*
	* @author matsubara
	*
	* @param int $sns_provider_id providerのID
	* @param string $redirectUrl 認証後のリダイレクトURL。
	* 	大体のSNS認証ではこのURLはSNS側の開発画面で許可するURLとして登録されていなければいけません。
	*
	* @return false|Object SNSのユーザー情報。OAuth処理上リダイレクトが発生する場合はfalseを返す。
	* 	・false の場合は Locationヘッダが送出されており、SNSサイトの認証画面へのリダイレクトが発生します。
	* 	　これを検知した場合はその他の出力を停止して、ただちにリダイレクトされるように処理する事をお勧めします。
	* 	　SNSサイトでの認証後に $redirectUrl にリダイレクトされ、再度このメソッドが実行される事で
	* 	　SNSのユーザー情報を返すことが出来ます。
	* 	・SNSのユーザー情報は HybridauthProviders の仕様でプロパティ名が決められており、
	* 	　取得できないデータであればempty（null）になっています。
	* 	　詳しくは→ https://hybridauth.github.io/hybridauth/apidoc.html を参照してください。
	* 	　参考に主に使用するであろうプロパティを以下に挙げておきます。
	* 		identifier: トークン
	* 		displayName: 表示名
	* 		email: メールアドレス
	*
	* @throws \App\Exception\SnsAuthException OAuth認証に失敗した場合。
	* 	ユーザーが認証をキャンセルしたり、SNS側の開発画面での設定に不備があったりすると失敗になります。
	*/
	public function OAuth($sns_provider_id, $redirectUrl){
        //まずプロバイダーIDでプロバイダーが取得できるか試行する
        $entSnsProvider = $this->find()->where([
			'id' => $sns_provider_id,
			'is_deleted' => false
		])->first();

		if( empty($entSnsProvider) ) throw new \App\Exception\SnsAuthException('指定されたSNSが不明です。');


        //OAuth認証
        $userProfile = \App\Lib\HybridauthProviders\Selector::getUserProfile($entSnsProvider->name, $redirectUrl);

        //SNSユーザー情報の取得に失敗
        if( empty($userProfile) ){
            //httpヘッダーに Location があれば、HybridauthProvidersのSelector::getUserProfileの処理で、
            //SNSサイトの認証画面にリダイレクトされたとして false を返して終了
            foreach( headers_list() as $head ){
                if( preg_match('`^\s*Location\s*:`i', $head) ) return false;
            }

            //Locationが無い場合はOAuth認証に失敗
            throw new \App\Exception\SnsAuthException('SNSユーザー情報の取得に失敗しました。');
        }

        return $userProfile;
    }

    /**
	* 最近接続したSNSプロバイダーのセッションをクリア
	* @author matsubara
	* @return bool 成功可否
	*/
	public function RecentlyAdapterDisconnect(){
		try{
			$RecentlyAdapter = \App\Lib\HybridauthProviders\Selector::getRecentlyAdapter();
			if( empty($RecentlyAdapter) == false ){
				$RecentlyAdapter->disconnect();
				return true;
			}

		}catch( \Exception $e ){
			//例外は無視、何もしない
		}

		return false;
	}

}
