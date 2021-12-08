<?php
/**
* Hybridauth Lineプロバイダー
* https://hybridauth.github.io | https://github.com/hybridauth/hybridauth
*
* id_tokenをデコードするためにFirebase\JWTのインストールが必要
* > composer require firebase/php-jwt
*
* Example:
* $config = [
* 	'callback' => Hybridauth\HttpClient\Util::getCurrentUrl(),
* 	'keys' => [ 'key' => '1653727815', 'secret' => 'e53f27154986e6c5cc203d5f66e81392' ],
* ];
* try {
* 	$adapter = new \App\Lib\HybridauthProviders\Line($config);
* 	$adapter->authenticate();
* 	$userProfile = $adapter->getUserProfile();
* }catch( Exception $e ){
* 	echo $e->getMessage() ;
* }
* @author mastubara
*/
namespace App\Lib\HybridauthProviders;

use \Firebase\JWT\JWT;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Exception\UnexpectedApiResponseException;
use Hybridauth\Data;
use Hybridauth\User;

/**
 * Line OAuth2 provider adapter.
 */
class Line extends OAuth2{
	/**
	 * {@inheritdoc}
	 */
	public $scope = 'openid email profile';

	/**
	 * {@inheritdoc}
	 */
	protected $apiBaseUrl = 'https://access.line.me/oauth2/v2.1';

	/**
	 * {@inheritdoc}
	 */
	protected $authorizeUrl = 'https://access.line.me/oauth2/v2.1/authorize';

	/**
	 * {@inheritdoc}
	 */
	protected $accessTokenUrl = 'https://api.line.me/oauth2/v2.1/token';

	/**
	 * {@inheritdoc}
	 */
	protected $apiDocumentation = 'https://developers.line.biz/ja/services/line-login/';


	//ユーザーデータ取得用のAPIのURL
	protected $userInfoUrl = 'https://api.line.me/v2/profile';


	/**
	 * {@inheritdoc}
	 */
	protected function initialize(){
		parent::initialize();
		//トークンリフレッシュ時に必要なパラメータを追加
		$this->tokenRefreshParameters['client_id'] = $this->clientId;
		$this->tokenRefreshParameters['client_secret'] = $this->clientSecret;
	}


	/**
	 * {@inheritdoc}
	 */
	protected function validateAccessTokenExchange($response){
		$collection = parent::validateAccessTokenExchange($response);

		//初回認証時に取得したid_tokenをセッションデータに書き込む。
		//このデータを元にユーザーのメールアドレスを取得し、セッションに書き込んでおく。
		//LINE APIの仕様でトークンリフレッシュ時にメールアドレスは取得できない
		//LINE APIのマニュアル → https://developers.line.biz/ja/docs/social-api/managing-access-tokens/#spy-anchor-a6ebc4386e71148f13948183cea979d85413d70c
		$id_token = $collection->get('id_token');
		$id_token_email = '';
		if( empty($id_token) == false ){
			//Lineから帰ってくる値から email が消え、有効期限が現在日時になった場合JWTだと例外が返ってくるため自前でデコード
			//$jwtDecoded = JWT::decode($id_token, $this->clientSecret, ['HS256']);
			$jwtDecoded = json_decode($this->base64UrlDecode( explode('.', $id_token)[1] ));

			if( empty(@$jwtDecoded->email) == false ){
				$id_token_email = $jwtDecoded->email;
			}
		}else{
			$id_token = $this->getStoredData('id_token');
			$id_token_email = $this->getStoredData('id_token_email');
		}

		//初回認証時のデータを書き込み
		//※リフレッシュ時もデータを再度セッションに書き込んでセッションの寿命をuser_profile_responseと同期させる
		$this->storeData('id_token', $id_token);
		$this->storeData('id_token_email', $id_token_email);


		//初回認証時、もしくはリフレッシュ時にユーザーデータを取得してセッションデータに保存
		$response = $this->apiRequest($this->userInfoUrl, 'GET');
		$this->storeData('user_profile_response', $response);

		return $collection;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getUserProfile(){
		$response = $this->getStoredData('user_profile_response');
//echo "<pre style='text-align:left; background-color:#ffc;'>"; var_dump($response); echo "</pre>";
		$data = new Data\Collection( $response );

		if( empty( $data->get('userId') ) ){
			throw new UnexpectedApiResponseException('Provider API returned an unexpected response.');
		}

		$userProfile = new User\Profile();

		$userProfile->identifier = $data->get('userId');
		$userProfile->displayName = $data->get('displayName');
		$userProfile->photoURL = $data->get('picture');
		$userProfile->description = $data->get('statusMessage');
		//emailは初回認証時に取得したデータを参照
		//※名前は更新されたがメールアドレスが更新されないとかあるかもしれない
		$userProfile->email = $this->getStoredData('id_token_email');

		return $userProfile;
	}


	/**
	* URL用エンコードされたbase64をデコード
	* @author matsubara
	* @param string $data base64Url化されている文字列
	* @return string デコードした文字列
	*/
	protected function base64UrlDecode($data){
		$pad = @strlen($data) % 4;
		return @base64_decode(
			str_replace(['-', '_'], ['+', '/'], $data)
			 . ($pad ? str_repeat('=', 4 - $pad) : '')
		);
	}
}
