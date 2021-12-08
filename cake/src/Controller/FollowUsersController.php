<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * FollowUsers Controller
 *
 * @property \App\Model\Table\FollowUsersTable $FollowUsers
 * @method \App\Model\Entity\Good[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FollowUsersController extends AppController
{

    /**
	 * フォローを追加
	 * @author matsubara
	 * Ajaxに出力
	 * @return  boolean
	 */
	public function add()
	{
		if( $this->request->is(['get','post']) == false) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('失敗');

		if(empty($this->Auth) || $this->Auth->status_id !== \App\Model\Table\UserStatusesTable::ID['NORMAL']){
			return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('フォローできる状態ではありません。');	
		}
		$this->autoRender = false;

		$follow_user_id = $this->request->getdata('follow_user_id');

		if( $this->Auth->id == $follow_user_id ) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('自身のアカウントにフォローはできません。');

		$follow_user = $this->FollowUsers->find()->where([
				'user_id' => $this->Auth->id,
				'follow_user_id' => $follow_user_id,
				'is_deleted' => false,
			])
			->first();	
		//フォローが既にある場合	
		if(empty($follow_user) == false) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('既にフォローされています。');

		$follow_user = $this->FollowUsers->find()->where([
				'user_id' => $this->Auth->id,
				'follow_user_id' => $follow_user_id,
				'is_deleted' => true,
			])
			->first();
		if( empty($follow_user) ){
			$follow_user = $this->FollowUsers->newEntity([
					'user_id' => $this->Auth->id,
					'follow_user_id' => $follow_user_id,
					'is_deleted' => false,
				]);
		}else{
			$follow_user->is_deleted = false;
		}

		if( $this->FollowUsers->save($follow_user) ) return $this->getResponse()->withType('text/plain')->withStringBody('フォローしました。');	

		return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('フォローができませんでした。');

	}

	/**
	 * フォローをキャンセル
	 * @author matsubara
	 * Ajaxに出力
	 * @return  boolean
	 */
    public function delete()
	{
		if( $this->request->is(['get','post']) == false) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('失敗');

		if(empty($this->Auth) || $this->Auth->status_id !== \App\Model\Table\UserStatusesTable::ID['NORMAL']){
			return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('削除できる権限がありません。');	
		}
		
		$this->autoRender = false;

		$follow_user_id = $this->request->getdata('follow_user_id');

		$follow_user = $this->FollowUsers->find()->where([
				'user_id' => $this->Auth->id,
				'follow_user_id' => $follow_user_id,
				'is_deleted' => false,
			])
			->first();	
		//フォローがない場合	
		if(empty($follow_user)) return $this->getResponse()->withType('text/plain')->withStringBody('フォローをしていません。');

		$follow_user->is_deleted = true;

		if($this->FollowUsers->save($follow_user)) return $this->getResponse()->withType('text/plain')->withStringBody('フォローの削除にしました。');	

		return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('失敗しました。');

	}
}
