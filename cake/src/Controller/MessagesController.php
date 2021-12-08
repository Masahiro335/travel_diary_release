<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\ORM\Table;
use Cake\I18n\Time;

/**
 * Messages Controller
 *
 * @property \App\Model\Table\MessagesTable $Messages
 * @method \App\Model\Entity\Message[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MessagesController extends AppController
{

	use \App\Controller\Traits\ImageTrait;

	/**
	 * 投稿一覧と検索処理
	 * @author matsubara
	 * 検索処理のみ(Ajaxに出力)
	 * @return  $this->render;
	 */
	public function index()
	{

		$this->paginate = [
			'limit' => 10,
		];
 
		//初期設定
		$conditions = ['Messages.is_deleted IN' => false];
		$orders = [
			'Messages.created' => 'desc',
			'Messages.id' => 'asc',
		];
		$this->Prefectures = \Cake\ORM\TableRegistry::getTableLocator()->get('Prefectures');

		//投稿を取得
		$queryMessages = $this->Messages->queryDefalut([
			'AuthGoods' => $this->Auth
		]);
	
		//検索
		if ( empty($this->request->getData()) == false ){

			//検索データを取得
			$get_data = $this->request->getData();
			//beginの日付がendの日付より大きい場合、endの日付をbeginの日付と同じにする。
			if( 
				!empty($get_data['begin'] ) && !empty($get_data['end'])
				&& $get_data['begin'] > $get_data['end']
			){
				$get_data['end'] = $get_data['begin'];
			}
			//日付検索
			if( empty($get_data['begin']) == false){
				$conditions[] = ['Messages.created >=' => $get_data['begin']. ' 00:00:00'];
			}
			if( empty($get_data['end']) == false){
				$conditions[] = ['Messages.created <=' =>  $get_data['end'].' 23:59:59.999999' ];
			}

			//フリーワード検索
			if( empty($get_data['freeWord']) == false){
				//1.検索キーワードの「両端のスペース」を削除。
				//2.検索キーワードの間の「全角スペースを半角スペース」に変更。
				//3.検索キーワードにスペースと[,]が入ってた場合、文字列を分裂する。
				$keyWords = preg_split('/[\s,]+/', mb_convert_kana(trim($get_data['freeWord']),'s'));
				//分裂されたキーワードを分裂事に検索。
				foreach ($keyWords as $keyWord){
					$freeWords[] = [
						'OR' => [
							'Users.name ILIKE' => '%'.$keyWord.'%',
							'Users.unique_id ILIKE' => '%'.$keyWord.'%',
							'Messages.message ILIKE' => '%'.$keyWord.'%',
						],
					];
				}
				$conditions[] = $freeWords;
			}

			//都道府県検索
			if( empty($get_data['prefectures-search']) == false){
				//投稿に検索する都道府県が含まれているか
				$PrefectureMessagesIds = \Cake\ORM\TableRegistry::getTableLocator()->get('PrefectureMessages')->find()
					->SELECT('message_id')
					->WHERE(['prefecture_id IN' => $get_data['prefectures-search'] ]);
				$conditions[] = ['Messages.id IN' => $PrefectureMessagesIds];
				$this->set('selectPrefectures', $get_data['prefectures-search']);
			}

			//フォローユーザーのみ
			if( empty($get_data['follow_check']) == false && empty($this->Auth) == false){
				$this->FollowUsers = \Cake\ORM\TableRegistry::getTableLocator()->get('FollowUsers');
				$FollowUserIds = $this->FollowUsers->find()
					->SELECT('follow_user_id')
					->where([
						'user_id' => $this->Auth->id,
						'is_deleted' => false,
					])
					;
				$conditions[] = ['Messages.user_id IN' => $FollowUserIds];
			}

			//Sort
			if(empty($get_data['order']) == false){
				switch ($get_data['order']){
					case '0':
						//最新順
						$orders = ['Messages.created' => 'desc'];
						break;
					case '1':
						//古い順
						$orders = ['Messages.created' => 'asc'];
						break;
					case '2':
						//いいねが多い順
						$queryMessages = $this->Messages->countGoods($queryMessages);
						$orders = [
							'count_goods' => 'desc',
							'Messages.created' => 'desc'
						];
						break;
					case '3':
						//コメントが多い順
						$queryMessages = $this->Messages->countComments($queryMessages);
						$orders = [
							'count_comments' => 'desc',
							'Messages.created' => 'desc'
						];
						break;
					default:
						$orders = ['Messages.created' => 'desc'];
						break;
				}
			}else{
				$orders = ['Messages.created' => 'desc'];
			}

		}

		$queryMessages = $queryMessages
			->where($conditions)
			->order($orders, true);
		$queryMessages = $this->paginate($queryMessages);

		$prefectures = $this->Prefectures->find('list')->toArray();
		$this->set(compact('queryMessages','prefectures'));
	}


	/**
	 * 投稿を保存(新規追加、投稿編集)
	 * @author matsubara
	 * Ajaxに出力
	 * @return  $this->render;
	 */
	public function edit($id = null)
	{

		if( $this->request->is('ajax') && $this->request->getData() ){
			//Template使用したレンダリングはしない
			$this->autoRender = false;

			if(empty($this->Auth) || $this->Auth->status_id !== \App\Model\Table\UserStatusesTable::ID['NORMAL']){
				return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('ログインをしてください。');	
			}

			if( empty($id) ){
				//追加
				$message = $this->Messages->newEmptyEntity();
				$message->user_id = $this->Auth->id;
			}else{
				//編集
				$message = $this->Messages->find()
						->where([
							'id' => $id,
							'user_id' => $this->Auth->id,
							'is_deleted' => false,
						])
						->first();
				$message->is_edit = true;
				//ユーザーの投稿ではない場合
				if( empty($message) ) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('ユーザーの投稿ではありません。');
			}
			$data = [];
			$data = $this->request->getData();
			//旅行先を保存
			$data['prefecture_messages'] = [];
			if(empty($data['prefectures-edit']) == false){
				foreach($data['prefectures-edit'] as $prefecture_id){
					$data['prefecture_messages'][] = ['prefecture_id' => $prefecture_id];
				}
			}
			$message = $this->Messages->patchEntity($message, $data, ['associated' => ['PrefectureMessages']] );

			//バリデーションエラー
			if( !empty($message->getErrors()) ){
				$errorMessages['errors'] = $message->getErrors();
				return  $this->response->withType('application/json')->withStringBody(json_encode($errorMessages));
				//バリデーションメッセージ追加したい場合
				//$errorMessages['errors']['message']['test'] = 'テスト';
			}

			//画像のバリデーション
			$images = $this->request->getData('images');
			if($images[0]->getClientFileName() !== ''){
				$image_count = 0;
				//現在の画像の数
				if(!empty($this->request->getData('image_count'))) $image_count = $this->request->getData('image_count');

				$getErrors = $this->imageValidation($images, $image_count, MESSAGES_IMAGE_MAX);

				//バリデーションエラー
				if( empty($getErrors) == false ){
					$errorMessages['errors'] = $getErrors;
					return $this->response->withType('application/json')->withStringBody(json_encode($errorMessages));
				}
			}

			//投稿を保存
			if($this->Messages->save($message) == false) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('投稿に失敗しました。');

			//画像の削除
			if(empty($this->request->getData('delete_image_ids')) == false){
				//ファイルを削除する画像のsortを取得
				$delete_image_ids = preg_split('/[,]+/', $this->request->getData('delete_image_ids'));

				$this->MessageImages = \Cake\ORM\TableRegistry::getTableLocator()->get('MessageImages');
				$this->MessageImages->imageDelete($message, $delete_image_ids, $this->Auth);
			}
			//画像のアップロード
			if($images[0]->getClientFileName() !== '' ){
				$this->MessageImages = \Cake\ORM\TableRegistry::getTableLocator()->get('MessageImages');
				$this->MessageImages->imageUpload($images, $message, $this->Auth);
			}

			$prefectures = \Cake\ORM\TableRegistry::getTableLocator()->get('Prefectures')->find('list')->toArray();

			//投稿を取得
			$message = $this->Messages->queryDefalut([
					'AuthGoods' => $this->Auth
				])
				->where(['Messages.id' => $message->id])
				->first();
		
			$this->set(compact('message','prefectures'));
			return $this->render('/element/messages/message');
		}
	}


	/**
	 * 投稿を削除
	 * @author matsubara
	 * Ajaxに出力
	 * @return  boolean
	 */
	public function delete($id = null)
	{
		if( $this->request->is('ajax') == false) return $this->redirect( ['action'=>'index'] );
	
		$this->autoRender = false;

		$message = $this->Messages->find()
				->where([
					'id' => $id,
					'user_id' => $this->Auth->id,
					'is_deleted' => false,
				])
				->first()
				;

		if(empty($message)) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('既に削除されている可能性があります。');

		//論理削除
		$message->is_deleted = true;

		if($this->Messages->save($message)){

			//画像を削除
			$this->MessageImages = \Cake\ORM\TableRegistry::getTableLocator()->get('MessageImages');
			$queryMessageImages = $this->MessageImages->find()->where([
				'message_id' => $message->id,
				'is_deleted' => false
			]);

			foreach($queryMessageImages as $entMessageImage){
				$fileName = $entMessageImage->id.'_'.$entMessageImage->image_upload_datetime->format('YmdHis').'.'.$entMessageImage->image_extension;
				if($entMessageImage->s3Delete($entMessageImage) == false) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('画像の削除に失敗しました。');
				$entMessageImage->is_deleted = true;
				$this->MessageImages->save($entMessageImage);
			}
			
			return $this->getResponse()->withType('text/plain')->withStringBody($id);
		}

		return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('投稿の削除が失敗しました。');
	}

}
