<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
	const LOGIN_SESSION_KEY = 'Auth';

	public function beforeFilter(EventInterface $event)
	{
		//ユーザーテーブル取得
		$this->Users = \Cake\ORM\TableRegistry::getTableLocator()->get('Users');
		//セッション
		$this->Session = $this->getRequest()->getSession();
		//ログインセッションがあった場合
		if($this->Session->check($this::LOGIN_SESSION_KEY) && $this::LOGIN_SESSION_KEY == 'Auth'){
			if($this->LoginUsersExport() == false){
				$this->LoginSessionDelete();
				$this->Flash->error('ログイン制限されているユーザーのためログインできません。');
				$this->redirect('/');
				return false;
			}
		}else{
			$this->Auth = '';
			$this->set('Auth','');
		}
		$this->set('add_display', true);
		$this->set('search_display', true);
		$this->set('map_display', true);
	}


	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading components.
	 *
	 * e.g. `$this->loadComponent('FormProtection');`
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		parent::initialize();

		$this->loadComponent('RequestHandler');
		$this->loadComponent('Flash');
	}

	/**
	 * ログインユーザーのセッションの書き込み
	 * 
	 * @author msubara
	 * @param Entity $entUser セッションにセットするユーザーのEntity
	 */
	protected function LoginSession( $entUser ):void{
		//不要項目はここで排除
		$entUser->setHidden(['password', 'modified', 'created']);
		//ログイン日時更新を追加
		$entUser->last_login_time = date('Y-m-d H:i:s');
		//セッションに書き込み
		$this->Session->write($this::LOGIN_SESSION_KEY, $entUser->toArray());
	}

	/**
	 * ログインユーザーのセッションを削除
	 * 
	 * @author msubara
	 * @param Entity $entUser セッションにセットするユーザーのEntity
	 */
	protected function LoginSessionDelete():void{
		//セッションを削除
		$this->Session->delete($this::LOGIN_SESSION_KEY);

		$this->Auth = null;
	}

	/**
	 * ログインユーザー情報を出力
	 * 
	 * @author msubara
	 * @return bool
	 */
	protected function LoginUsersExport(){
		$entUser = $this->Session->read($this::LOGIN_SESSION_KEY);
		//ログインユーザー情報を代入
		$this->Auth = $this->Users
			->queryDefalut()
			->where(['Users.id' => $entUser['id']])
			->first();
		$this->LoginSession($this->Auth);

		if(
			in_array($this->Auth->status_id, [
				\App\Model\Table\UserStatusesTable::ID['NORMAL'],
				\App\Model\Table\UserStatusesTable::ID['RESTRICTION'],
			]) == false
		){
			return false;
		}
		$this->set('Auth',$this->Auth);
		return true;
	}
}
