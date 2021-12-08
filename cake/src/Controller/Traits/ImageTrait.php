<?php
namespace App\Controller\Traits;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

trait ImageTrait{

	/**
	 * 画像のバリデーションチェック
	 * @param file $images 画像データ複数
	 * @param int $image_count 現在の画像数
	 * @param int $image_max 最大画像数
	 * @author matsubara
	 * @return  エラー文字列
	 */
	public function imageValidation($images, $image_count = null, $image_max = null)
	{
		$getErrors  = null;

		//ファイルの数をチェック
		if( @count($images) + $image_count > $image_max){
			$getErrors['image']['count'] = '画像は4枚までアップロード可能です。';
			return $getErrors;
		}

		foreach($images as $image){
			//拡張子をチェック
			if( preg_match('/png|jpg|jpeg/',$image->getClientMediaType()) === false){
				//画像拡張子が当てはまらない
				$getErrors['image']['extension'] = '無効な画像形式です。';
				return $getErrors;
			}
			//サイズをチェック
			if( $image->getSize() > 10000000 || $image->getSize() == 0){
				//画像拡張子が当てはまらない
				$getErrors['image']['size'] = '画像サイズが大きすぎます。(10MB未満の画像を選択してください。)';
				return $getErrors;
			}
		}

		return '';
	}


	//GDで画像をリサイズ　今後使用する可能性あり。
	/**
	* GDで画像をリサイズ
	* @author matsubara
	* @throws \Exception $imageDataがサポートしない画像形式だった場合
	*/
	/*
	protected function imageResize($images, $containerWidth, $containerHeight){
		$containerHeight = 200; // リサイズしたい大きさを指定
		$containerWidth = 200;
				
		foreach($images as $image){	
			$imageData = $image->getStream()->getContents();
			list($originalWidth, $originalHeight) = getimagesizefromstring($imageData);
			$originalType = $image->getClientMediaType();
				
			$qdImage = imagecreatefromstring( $imageData );
	
			//リサイズ後のサイズをコンテナサイズに合わせる
			$ratio = $originalWidth / $originalHeight;
			$containerRatio = $containerWidth / $containerHeight;
			if( $ratio > $containerRatio ){
				$resizeWidth = $containerWidth;
				$resizeHeight = (int)floor($containerWidth / $ratio);

			}else{
				$resizeWidth = (int)floor($containerHeight * $ratio);
				$resizeHeight = $containerHeight;
			}
			
			// 新しく描画するキャンバスを作成
			$gdCanvas = imagecreatetruecolor($resizeWidth, $resizeHeight);
			imagecopyresampled($gdCanvas, $gdImage, 0,0,0,0, $resizeWidth, $resizeHeight, $originalWidth, $originalHeight);

			switch( $originalType ){
				case 'image/png':
					imagepng( $gdCanvas );
					break;
	
				case 'image/gif':
					imagegif( $gdCanvas );
					break;
	
				case 'image/jpeg':
				default: //不明な場合はjpeg
					imagejpeg( $gdCanvas );
			}

			@imagedestroy($gdImage);
			@imagedestroy($gdCanvas);
			return $gdCanvas;
		}
	}
	*/

}