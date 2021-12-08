<?php
/**
* Hybridauth YahooJapanプロバイダー
* https://hybridauth.github.io | https://github.com/hybridauth/hybridauth
*
* Example:
*
* $config = [
* 	'callback' => Hybridauth\HttpClient\Util::getCurrentUrl(),
* 	'keys' => [ 'key'=>'dj00aiZpPVVmOW1mTWZ2QkNDQiZzPWNvbnN1bWVyc2VjcmV0Jng9Mzg-', 'secret'=>'dJEUMIfP8YqgKSwLuqxKGizqEJQs6KckJsf7wfSm' ],
* ];
* try {
* 	$adapter = new \App\Lib\HybridauthProviders\YahooJapan($config);
* 	$adapter->authenticate();
* 	$userProfile = $adapter->getUserProfile();
* }catch( Exception $e ){
* 	echo $e->getMessage() ;
* }
* @author matsubara
*/
namespace App\Lib\HybridauthProviders;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Exception\UnexpectedApiResponseException;
use Hybridauth\Data;
use Hybridauth\User;

/**
 * YahooJapan OAuth2 provider adapter.
 */
class YahooJapan extends OAuth2{
	/**
	 * {@inheritdoc}
	 */
	public $scope = 'openid email profile address';

	/**
	 * {@inheritdoc}
	 */
	protected $apiBaseUrl = 'https://auth.login.yahoo.co.jp/yconnect/v2';

	/**
	 * {@inheritdoc}
	 */
	protected $authorizeUrl = 'https://auth.login.yahoo.co.jp/yconnect/v2/authorization';

	/**
	 * {@inheritdoc}
	 */
	protected $accessTokenUrl = 'https://auth.login.yahoo.co.jp/yconnect/v2/token';

	/**
	 * {@inheritdoc}
	 */
	protected $apiDocumentation = 'https://developer.yahoo.co.jp/yconnect/v2/';


	//ユーザーデータ取得用のAPIのURL
	protected $userInfoUrl = 'https://userinfo.yahooapis.jp/yconnect/v2/attribute';


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

		//最初の認証時、もしくはリフレッシュ時にユーザーデータを取得してセッションデータに保存
		$response = $this->apiRequest($this->userInfoUrl, 'GET');
		$this->storeData('user_profile_response', $response);

		return $collection;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getUserProfile(){
		$response = $this->getStoredData('user_profile_response');
		$data = new Data\Collection( $response );

		if( empty( $data->get('sub') ) ){
			throw new UnexpectedApiResponseException('Provider API returned an unexpected response.');
		}

		$userProfile = new User\Profile();

		$userProfile->identifier = $data->get('sub');
		$userProfile->displayName = $data->get('nickname') != '' ? $data->get('nickname') : $data->get('name');
		$userProfile->photoURL = $data->get('picture');
		$userProfile->email = $data->get('email');
		$userProfile->emailVerified = $data->get('email_verified');
		$userProfile->birthYear = $data->get('birthdate');
		$userProfile->country = $data->get('address')->country;
		$userProfile->zip = $data->get('address')->postal_code;
		$userProfile->lastName = $data->get('family_name');
		$userProfile->firstName = $data->get('given_name');
		$userProfile->gender = $data->get('gender');
		$userProfile->address = $data->get('address')->formatted;
		$userProfile->region = $data->get('address')->region;
		$userProfile->city = $data->get('address')->locality;
		$userProfile->identifier = $data->get('sub');
		$userProfile->identifier = $data->get('sub');

		return $userProfile;
	}

}
