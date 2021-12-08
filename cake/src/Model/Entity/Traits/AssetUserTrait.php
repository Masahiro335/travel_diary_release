<?php
namespace App\Model\Entity\Traits;
use Laminas\Diactoros\UploadedFile;
use Cake\I18n\FrozenTime;
use Aws\S3\S3Client;

/**
 * Asset Trait
 * 
 * 画像処理を必要とするいくつかのテーブルに、同じ機能を提供する。
 * AWS S3へのアップロードやURLの生成など。
 * 
 * @author matsubara
 */
trait AssetUserTrait
{
	//S3接続をキャッシュする
	protected static $_s3client;


	/**
	 * S3に接続してクライアントオブジェクトを生成
	 * @author matsubara
	 * @return Aws\S3\S3Client
	 */
	protected function s3Open(){
		if( empty($this::$_s3client) == false ) return $this::$_s3client;

		$sdk = new \Aws\Sdk([
			'credentials' => [
				'key' => AWS_S3_ACCESS_TOKEN,
				'secret' => AWS_S3_ACCESS_SECRET,
			],
			'region' => AWS_S3_REGION,
			'version' => 'latest',
		]);

		return $this::$_s3client = $sdk->createS3();
	}


	/**
	 * S3ダウンロード
	 * @author matsubara
	 * @param entity $entity
	 * @return array | false 成功した場合アップロード情報、失敗はfalse
	 * @throws \Exception 例外が発生した場合
	 */
	public function s3Download( $entity ){
		if(empty($entity)) return false;

		//S3ダウンロード
		$getObject = $this->s3Open()->getObject([
			'Bucket' => AWS_S3_BUCKETNAME,
			'Key' => $this->createURL($entity),
		]);
		if(empty($getObject['Body']->getContents()) == false){
			return $getObject['Body']->getContents();
		}

		return false;
	}

	/**
	 * S3へユーザー画像のアップロード
	 * @author matsubara
	 * @param string $image 画像データ
	 * @param entity $entity
	 * @param string $iconOrhome icon or home
	 * @return array | false 成功した場合アップロード情報、失敗はfalse
	 */
	public function s3UserUpload( $image, $entity ,$iconOrhome){
		if(empty($image) || empty($entity)) return false;

		//S3にアップロード
		$putObject = $this->s3Open()->putObject([
			'ACL' => 'public-read',
			'Bucket' => AWS_S3_BUCKETNAME,
			'Key' => $this->createUserURL($entity, $iconOrhome),
			'Body' => $image->getStream()->getContents(),
			'ContentType' => $image->getClientMediaType(),
		]);
		if(empty($putObject['ObjectURL']) == false){
			return $putObject['ObjectURL'];
		}

		return false;
	}

	/**
	* S3のユーザー画像リンクの作成
	* @author matsubara
	* @param entity $entity
	* @param string $iconOrhome icon or home
	*/
	public function createUserURL($entity, $iconOrhome){
		if(empty($entity)) return '';

		//アイコン画像
		if($iconOrhome == ICON){
			return $this::ASSET_FOLDER_NAME.'/'.ICON.'/'.$entity->id.'_'.$entity->icon_upload_datetime->format('YmdHis').'.'.$entity->icon_extension;
		//ホーム画像
		}else{
			return $this::ASSET_FOLDER_NAME.'/'.HOME.'/'.$entity->id.'_'.$entity->home_upload_datetime->format('YmdHis').'.'.$entity->home_extension;
		}
	}


	/**
	* S3のユーザーのオブジェクトURLの作成
	* @param string $iconOrhome icon or home
	*/
	public function userImage($iconOrhome)
	{
		return AWS_S3_ENDPOINT . $this->createUserURL($this, $iconOrhome);
	}

	/**
	* S3のユーザーのオブジェクトURLの作成
	* @param entity $entity
	* @param string $iconOrhome icon or home
	*/
	public function s3UserDelete($entity, $iconOrhome)
	{
		if(empty($entity)) return false;

		try{
			$result = $this->s3Open()->deleteObject([
				'Bucket' => AWS_S3_BUCKETNAME,
				'Key'    => $this->createUserURL($entity, $iconOrhome),
			]);
		}catch( \Exception $e ){
			return false;
		}

		return true;
	}
}
