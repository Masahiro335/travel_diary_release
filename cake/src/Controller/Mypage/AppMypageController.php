<?php
namespace App\Controller\Mypage;

class AppMypageController extends \App\Controller\AppController
{


	/**
	* アクション実行後にビューが描画される前の処理
	* @param Cake\Event\EventInterface $event Cakeのイベント
	*/
	public function beforeFilter($event){
		parent::beforeFilter($event);
        if(empty($this->Auth)){
			$this->Flash->error('ログイン情報が取得できませんでした。');
			return $this->redirect('/');
		}
	}
}
