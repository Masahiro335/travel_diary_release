<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\Table;

/**
 * PrefecturesController Controller
 *
 * @property \App\Model\Table\PrefecturesTable $Prefectures
 * @method \App\Model\Entity\Good[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PrefecturesController extends AppController
{

	/**
	 * 旅行先を取得
	 * @author matsubara
	 * Ajaxに出力
	 * @return  array 旅行先
	 */
	public function userPrefectures()
	{
		if( $this->request->is('ajax') == false ) return $this->redirect('/');
		$this->autoRender = false;

		//ユーザーが行った旅行先
		$user_id = $this->request->getData('user_id');
		$user = $this->Users->get($user_id);
		if(empty($user) || $user->is_deleted == true) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('データの取得に失敗しました。');
 
		$arrayPrefectureIds = \Cake\ORM\TableRegistry::getTableLocator()->get('PrefectureMessages')->userPrefectures($user_id);

        //旅行先情報を取得
		$queryPrefectures = $this->Prefectures->find();
		$arrayPrefectures = [];
        foreach($queryPrefectures as $entPrefecture){
            $arrayPrefectures[] = [
				'code' => $entPrefecture->id,
				'name' => $entPrefecture->name,
				'number' => $entPrefecture->userPrefecturescount($arrayPrefectureIds),
				'user_id' => $user_id,
			];
        }
        return $this->response->withType('application/json')->withStringBody(json_encode($arrayPrefectures));

	}

	/**
	 * 指定したユーザーの旅行先を表示
	 * @author matsubara
	 * Ajaxに出力
	 * @return  render 旅行先一覧
	 */
	public function userSelect()
	{
		if( $this->request->is('ajax') == false ) return $this->redirect('/');
		$this->autoRender = false;

		//選択した旅行先ID
		$prefecture_id = $this->request->getData('prefecture_id');
		$user_id = $this->request->getData('user_id');
		$user = $this->Users->get($user_id);
		if(empty($user) || $user->is_deleted == true) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('データの取得に失敗しました。');

		//投稿を取得
		$messages = \Cake\ORM\TableRegistry::getTableLocator()->get('Messages')->queryDefalut([
			'AuthGoods' => $this->Auth,
			'UsersId' => $user->id,
			'PrefectureSelect' => $prefecture_id,
		])
		->orderDesc('Messages.created');
	
		if($messages->count() == 0) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('投稿がありません。');

		$prefectures = $this->Prefectures->find('list')->toArray();
		$this->set(compact('messages','prefectures'));
		//ajaxに出力
		return $this->render('/element/messages/messages');

	}

	/**
	 * 今月もマップを表示
	 * @author matsubara
	 * Ajaxに出力
	 * @return  array 旅行先
	 */
	public function index()
	{
		if( $this->request->is('ajax') ){
			$this->autoRender = false;

			$month = $this->request->getData('month');
			if( empty($month) ) $month = date('Y-m');

			$is_follow_check = $this->request->getData('follow_check');
			if( empty($is_follow_check) ) $is_follow_check = false;

			//旅行先情報を取得
			$queryPrefectures = $this->Prefectures->find();
			$arrayPrefectures = [];
			foreach($queryPrefectures as $entPrefecture){
				$arrayPrefectures[] = [
					'code' => $entPrefecture->id,
					'name' => $entPrefecture->name,
					'number' => $entPrefecture->monthPrefecturesCount($month, $is_follow_check, empty($this->Auth)? '':$this->Auth),
				];
			}
			return $this->response->withType('application/json')->withStringBody(json_encode($arrayPrefectures));
		}

		$this->set('prefectures', $this->Prefectures->find('list')->toArray());
	}


	/**
	 * 選択した旅行先を表示
	 * @author matsubara
	 * Ajaxに出力
	 * @return  render 旅行先一覧
	 */
	public function select()
	{
		if( $this->request->is('ajax') == false ) return $this->redirect('/');

		$this->autoRender = false;
		if( empty( $this->request->getData()) )  return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('データの取得に失敗しました。');

		//選択した旅行先ID
		$prefecture_id = $this->request->getData('prefecture_id');
		//指定した月
		$month = $this->request->getData('month');

		//投稿を取得
		$messages = \Cake\ORM\TableRegistry::getTableLocator()->get('Messages')->queryDefalut([
			'AuthGoods' => $this->Auth,
			'PrefectureSelect' => $prefecture_id,
		])
		->where([
			'Messages.created >=' => date('Y-m-d', strtotime('first day of'.($month.' 00:00:00'))),
			'Messages.created <=' => date('Y-m-d', strtotime('last day of'.($month.' 23:59:59.999999'))),
		])
		->orderDesc('Messages.created');
	
		if($messages->count() == 0) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('投稿がありません。');

		$prefectures = $this->Prefectures->find('list')->toArray();
		$this->set(compact('messages','prefectures'));
		//ajaxに出力
		return $this->render('/element/messages/messages');

	}
}
