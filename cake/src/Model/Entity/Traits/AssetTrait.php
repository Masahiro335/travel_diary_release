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
trait AssetTrait
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
	 * S3へアップロード
	 * @author matsubara
	 * @param string $image 画像データ
	 * @param entity $entity
	 * @return array | false 成功した場合アップロード情報、失敗はfalse
	 */
	public function s3Upload( $image, $entity ){
		if(empty($image) || empty($entity)) return false;

		//S3にアップロード
		$putObject = $this->s3Open()->putObject([
			'ACL' => 'public-read',
			'Bucket' => AWS_S3_BUCKETNAME,
			'Key' => $this->createURL($entity),
			'Body' => $image->getStream()->getContents(),
			'ContentType' => $image->getClientMediaType(),
		]);
		if(empty($putObject['ObjectURL']) == false){
			return $putObject['ObjectURL'];
		}

		return false;
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
	* S3画像の削除
	* @author sgk
	* @param entity $entity
	*/
	public function s3Delete( $entity = null){
		if(empty($entity)) return false;

		try{
			$result = $this->s3Open()->deleteObject([
				'Bucket' => AWS_S3_BUCKETNAME,
				'Key'    => $this->createURL($entity),
			]);
		}catch( \Exception $e ){
			return false;
		}

		return true;
	}


	/**
	* S3のリンクの作成
	* @author matsubara
	* @param entity $entity
	*/
	public function createURL( $entity ){
		if(empty($entity->image_upload_datetime)) return '';

		return $this::ASSET_FOLDER_NAME.'/'.$entity->id.'_'.$entity->image_upload_datetime->format('YmdHis').'.'.$entity->image_extension;
	}


	/**
	* S3のオブジェクトURLの作成
	*/
	protected function _getImage(): ?string
	{
		return AWS_S3_ENDPOINT . $this->createURL( $this );
	}

}
