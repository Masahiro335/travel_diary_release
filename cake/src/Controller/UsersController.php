<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\Table;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\Message[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

	/**
	 * ユーザー詳細画面
	 * @author matsubara
	 * @return 
	 */
	public function view($id = null)
	{
		$user = $this->Users->queryDefalut()
			->where(['Users.id' => $id])
			->first()
			;
	
		if( empty($user) ){
			$this->Flash->error('ユーザーの情報が取得できませんでした。');
			return $this->redirect('/');
		}
		//ユーザーの投稿
		$this->Messages = \Cake\ORM\TableRegistry::getTableLocator()->get('Messages');
		$messages = $this->Messages->UsersMessages($user, $this->Auth);

		//ユーザーが「いいね」した投稿
		$goodMessages = $this->Messages->UsersGoodsMessages($user, $this->Auth);
	
		$this->Prefectures = \Cake\ORM\TableRegistry::getTableLocator()->get('Prefectures');
		$prefectures = $this->Prefectures->find('list')->toArray();
		$this->set(compact('user','messages','goodMessages','prefectures'));
    }

}
