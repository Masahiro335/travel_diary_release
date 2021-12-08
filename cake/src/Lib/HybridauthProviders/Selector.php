<?php
/**
* Hybridauthの選択的プロバイダー
*
* ・独自定義のプロバイダーもしくは元々ライブラリに付属しているプロバイダーを
* 　プロバイダー名（クラス名）を指定してアダプターの取得や操作するためのクラス
*
* ・定義情報も記載しているのでプロバイダーの追加やキー名の変更などを行う場合は
* 　このクラスの定義部分（Selector::$config）を編集する必要があります。
* 　$configに定義が無いプロバイダーを指定してメソッドをコールすると例外になります。
* 
* Example:
* $userProfile = \App\Lib\HybridauthProviders\Selector::getUserProfile('Line');
* @author matsubara
* 
*/
namespace App\Lib\HybridauthProviders;

use \Hybridauth\Hybridauth;

class Selector{

	/**
	* プロバイダーの設定情報
	* 認証プロバイダーを追加・編集する場合はこの配列の定義を編集してください
	* ※追加・編集する場合は \App\Controller\OauthController::_getProviderParams() の方も確認してください。
	* 
	* Example:
	* <pre>{@code
	* $config = [
	* 	//実行時にパラメータで指定できるので必須ではない。
	* 	'_default_callback_url' => 'プロバイダー別のコールバックURLの定義が無い場合に適用されるコールバックURL。',
	*
	* 	'プロバイダー名、\App\Lib\HybridauthProviders か、\Hybridauth\Provider 内のクラス名' => [
	* 		※定義するキーや値はそれぞれプロバイダーによって違うので、
	* 		　Hybridauth のドキュメントや例、各プロバイダークラスのソースコードなどを参考にしてください。
	* 		※設定例
	* 		  ↓
	* 		'keys' => [
	* 			'key'=>'abcdefg',
	* 			'secret'=>'0123456789abcdefg',
	* 		],
	* 		'callback' => '任意：コールバックURL、主に引数でセットするので定義することはまず無い。',
	* 	],
	* ];
	* }</pre>
	* @author matsubara
	*/
	static protected $config = [
		'_default_callback_url' => SYSTEM_URL,

		//Lineは「Line Developers」の「LINEログイン設定」の 「コールバックURL」が / で終わっていないと
		//「Invalid redirect_uri value」のエラーになるのでここにセットするcallbackのURLも合わせて末尾に / を付ける事
		'Google' => [
			'keys' => OAUTH_KEYS['GOOGLE'],
		],
		'Twitter' => [
			'keys' => OAUTH_KEYS['TWITTER'],
		],
		'Line' => [
			'keys' => OAUTH_KEYS['LINE'],
		],
	];


	static protected $_recentlyAdapter = null;


	/**
	* プロバイダーの設定情報を取得
	*
	* @author mastubara
	* @param string $providerName 設定情報を取得するプロバイダー名、指定しなかった場合は Selector::$config をそのまま返す
	* @param string $callbackUrl 強制的にコールバックURLをセットする場合
	*
	* @return array 選択された設定情報
	*
	* @throw Exception 指定したプロバイダーの設定情報が無い場合
	*/
	static public function getConfig($providerName = null, $callbackUrl = null){
		if( $providerName === null ){
			return static::$config;
		}

		if( isset(static::$config[$providerName]) == false ){
			throw new \Exception('指定されたプロバイダーの設定情報がありません。');
		}

		$selectedConfig = static::$config[$providerName];

		//引数で指定がある場合は強制的にconfigのコールバックURLを上書き
		if( $callbackUrl !== null ){
			$selectedConfig['callback'] = $callbackUrl;

		//プロバイダー別のconfigにコールバック定義ない場合はデフォルトコールバック設定を使う
		}else if( array_key_exists('callback', $selectedConfig) == false ){
			$selectedConfig['callback'] = static::$config['_default_callback_url'];
		}

		return $selectedConfig;
	}


	/**
	* 指定した認証プロバイダーのアダプターを取得
	*
	* @author mastubara
	* @param string $providerName 設定情報を取得するプロバイダー名
	* @param string $callbackUrl 強制的に認証時コールバックURLをセットする場合
	*
	* @return Hybridauth\Adapter 指定したプロバイダーのアダプター（\Hybridauth\Adapter を基底とする接続クラスのインスタンス）
	*
	* @throw Exception 指定したプロバイダーの設定情報が無い場合
	*/
	static public function getAdapter($providerName, $callbackUrl = null){
		$selectedConfig = static::getConfig($providerName, $callbackUrl);

		//独自に作成したプロバイダーのクラス
		$classHybridauthProvider = '\App\Lib\HybridauthProviders\\'.$providerName;

		//独自プロバイダーが無い場合はHybridauth\Provider以下のクラス
		if( class_exists( $classHybridauthProvider ) == false ){
			$classHybridauthProvider = '\Hybridauth\Provider\\'.$providerName;
		}

		$adapter = new $classHybridauthProvider( $selectedConfig );
		return $adapter;
	}


	/**
	* 指定した認証プロバイダーのログインしたユーザーのデータを取得
	*
	* ・アダプターの取得、認証の処理、UserProfileの取得をまとめて行います。
	* ・アダプターを取得以降の処理に例外が発生した場合はfalseを返します。
	* 　例外が発生した場合はdisconnectを試行します。
	* 　これによりそのプロバイダーのログインセッションがクリアされます。
	*
	* @author mastubara
	* @param string $providerName ユーザーデータを取得するプロバイダー名
	* @param string $callbackUrl 強制的に認証時コールバックURLをセットする場合
	* @param int $timesToTry 取得を試行する回数、指定しない場合は2回。
	* 	2回試行する理由は、1回目の試行で失敗した場合、
	* 	ログインセッションをクリアして改めてauthenticateを実行することにより
	* 	プロバイダーのログイン認証画面が表示される事を期待するため。
	*
	* @return Hybridauth\User\Profile | bool 指定したプロバイダーのUserProfile、失敗した場合は false
	*
	* @throw Exception アダプターの取得で問題があった場合
	*/
	static public function getUserProfile($providerName, $callbackUrl = null, $timesToTry = 2){
		$adapter = static::getAdapter($providerName, $callbackUrl);
		static::$_recentlyAdapter = $adapter;

		//ユーザーデータが取得できるまで指定回数実行
		for($ni=0; $ni < $timesToTry; $ni++){
			try{
				$adapter->authenticate();

				return $adapter->getUserProfile();
			}catch( \Exception $e ){
				$adapter->disconnect();
			}
		}

		return false;
	}


	/**
	* @author mastubara
	* @return Hybridauth\Adapter | null 最近接続したプロバイダーのアダプター
	*/
	static public function getRecentlyAdapter(){
		return static::$_recentlyAdapter;
	}


	/**
	* コンストラクター
	* インスタンスは使わない
	* staticメソッド（Selector::getAdapter('adapter名') など）をコールしてください。
	*
	* @author mastubara
	* @throw Exception 「new Selector()」すると例外投げる
	*/
	public function __construct(){
		throw new \Exception('static method only');
	}

}
