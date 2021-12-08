<?php
declare(strict_types=1);

namespace App\Controller\Mypage;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\ORM\Table;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
use Cake\Mailer\Mailer;
use Cake\Mailer\MailerAwareTrait;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppMypageController
{

	use \App\Controller\Traits\ImageTrait;
	use MailerAwareTrait;

	/**
	 * マイページ
	 * @author matsubara
	 */
	public function index()
	{
		//ユーザーの投稿
		$this->Messages = \Cake\ORM\TableRegistry::getTableLocator()->get('Messages');
		$messages = $this->Messages->UsersMessages($this->Auth, $this->Auth);

		//ユーザーが「いいね」した投稿
		$goodMessages = $this->Messages->UsersGoodsMessages($this->Auth, $this->Auth);
	
		$this->Prefectures = \Cake\ORM\TableRegistry::getTableLocator()->get('Prefectures');
		$prefectures = $this->Prefectures->find('list')->toArray();

		$this->set('user',$this->Auth);
		$this->set(compact('messages','goodMessages','prefectures'));
		$this->set('nav', NAV_MYPAGE);
		//ユーザー詳細画面
		$this->render('/Users/view');
	}

	/**
	 * 
	 * プロフィール
	 * @param 
	 */
	public function profile()
	{
		$user = $this->Users->find()
			->where(['id' => $this->Auth->id])
			->first()
			;

		if( empty($user) ){
			$this->Flash->error('ユーザーの情報が取得できませんでした。');
			return $this->redirect('/');
		}

		if( empty($this->request->getData()) == false ){		
			$user = $this->Users->patchEntity(
				$user, 
				$this->request->getData(), 
				['validate'=>'ProfileUpdate']
			);

			if( $this->Users->save($user) ){
				//アイコン画像
				if( empty($this->request->getData('icon')->getClientFileName()) == false){
					$this->profileImageUplaod(ICON, $user);
				}
				//アイコン画像削除
				if(empty($this->request->getData('delete_icon')) == false){
					if($user->s3UserDelete($user, ICON)){
						$user->is_icon = false;
						$this->Users->save($user);
					}
				}

				//ホーム画像
				if( empty($this->request->getData('home')->getClientFileName()) == false ){
					$this->profileImageUplaod(HOME, $user);
				}
				//ホーム画像削除
				if( empty($this->request->getData('delete_home')) == false ){
					if($user->s3UserDelete($user, HOME)){
						$user->is_home = false;
						$this->Users->save($user);
					}
				}
	
				$this->Flash->success('プロフィールを作成しました。');
				return $this->redirect(['action' => 'index']);
			}
			$this->Flash->error('プロフィールの作成に失敗しました。');
		}
		$this->Prefectures = \Cake\ORM\TableRegistry::getTableLocator()->get('Prefectures');
		$prefectures = $this->Prefectures->find('list')->toArray();
		$this->set(compact(['user','prefectures']));
		$this->set('nav', NAV_PROFILE);
		$this->set('add_display', false);
		$this->set('search_display', false);
		$this->set('map_display', false);
	}

	/**
	 * 
	 * プロフィール画像のアップロード
	 * @param string $iconOrhome アイコン or ホーム
	 * @return bool 成功 or 失敗
	 */
	public function profileImageUplaod($iconOrhome = null, $user = null)
	{

		$connection = \Cake\Datasource\ConnectionManager::get('default');
		$connection->begin();
		try{
	
			//画像データ取得
			$image = $this->request->getData($iconOrhome);
			if( empty($image->getClientFileName()) )  throw new \Exception('error: 取得できませんでした。');

			//バリデーション
			if(empty($this->imageValidation($image, 0, USERS_IMAGE_MAX)) == false) throw new \Exception('error: バリデーション');


			//既存の画像を削除
			if(
				$iconOrhome == ICON && $user->is_icon == true
				|| $iconOrhome == HOME && $user->is_home == true
			){
				if($user->s3UserDelete($user, $iconOrhome) == false) throw new \Exception('error: 画像削除に失敗しました。');
			}

			$user = $this->Users->patchEntity(
				$user, 
				[
					'name' => $user->name,
					'is_'.$iconOrhome => true,
					$iconOrhome.'_upload_datetime' => new FrozenTime(),
					$iconOrhome.'_extension' => pathinfo($image->getClientMediaType())['basename'],
				], 
				['validate'=>'ProfileUpdate']
			);
		
			
			if($this->Users->save($user) == false) throw new \Exception('error: 保存に失敗しました。');

			//画像アップ
			if($user->s3UserUpload($image, $user, $iconOrhome) == false) throw new \Exception('error: 画像アップに失敗しました。');
		
			$connection->commit();
			return true;

		}catch( \Exception $e ){
			$connection->rollback();
			return false;
		}
	}

	/**
	 * 設定
	 * @author matsubara
	 *
	 * @return 
	 */
	public function setting()
	{
		$this->set('nav', NAV_SETTING);
		$this->set('add_display', false);
		$this->set('search_display', false);
	}


	/**
	 * メールアドレスの変更　メール送信
	 * @author matsubara
	 *
	 * @return 
	 */
	public function emailEdit()
	{

		if( empty($this->request->getData()) == false ){
			$email = $this->request->getData('email');
			if( empty($email) ){
				$this->Flash->error('メールアドレスの取得に失敗しました。');
				return $this->redirect(['action' => 'emailEdit']);
			}

			
			$targetUser = $this->Users->find()->where(['email' => $email])->first();
			$snsUser = \Cake\ORM\TableRegistry::getTableLocator()->get('SnsTokens')->find()->where(['email' => $email])->first();

			if( empty($targetUser) == false || empty($snsUser) == false){
				$this->Flash->error('既にこのメールアドレスは登録されています。');
				return $this->redirect(['action' => 'emailEdit']);
			}

			$entUser = $this->Users->get($this->Auth->id);
			$entUser->tmp_mail_token = $this->Users->userUniqueCode(12);
			$entUser->token_limit_time = new Time(REGISTER_LIMIT_TIME);

			if( $this->Users->save($entUser) ){
				//OpenSSLでデータを暗号化
				$urlEncoded = $this->Users->sslEncrypt(
					$entUser->id,
					$email,
					$entUser->tmp_mail_token
				);
	
				//メール送信
				$this->getMailer('User')->send('emailEdit', [$email, $urlEncoded]);
				$this->Flash->success('メールアドレスを送信しました。送信された登録メールのリンクをクリックしてください。');
				return $this->redirect(['action' => 'index']);
			}
		}

		$this->set('nav', NAV_SETTING);
		$this->set('add_display', false);
		$this->set('search_display', false);
	}


	/**
	 * パスワード変更
	 * @author matsubara
	 *
	 * @return 
	 */
	public function passwordEdit()
	{

		if( empty($this->request->getData()) == false ){
			$password = $this->request->getData('password');
			if( empty($password) ){
				$this->Flash->error('パスワードの取得に失敗しました。');
				return $this->redirect(['action' => 'passwordEdit']);
			}

			$entUser = $this->Users->get($this->Auth->id);
			$entUser = $this->Users->patchEntity(
				$entUser,
				['password' => $password],
				['validate'=>'PasswordEdit']
			);

			if( $this->Users->save($entUser) ){
				$this->Flash->success('パスワードの変更に成功しました。');
				return $this->redirect(['action' => 'index']);
			}
			$this->Flash->error('パスワードの変更に失敗しました。');
		}

		$this->set('nav', NAV_SETTING);
		$this->set('add_display', false);
		$this->set('search_display', false);
	}


	/**
	 * 退会
	 * @author matsubara
	 *
	 * @return 
	 */
	public function withdrawal()
	{
		if( empty($this->request->getData()) == false ){

			$entUser = $this->Users->get($this->Auth->id);
			$entUser->is_deleted = true;
			$entUser->status_id = \App\Model\Table\UserStatusesTable::ID['WITHDRAWAL'];

			if( $this->Users->save($entUser) ){
				//ログを追加
				\Cake\ORM\TableRegistry::getTableLocator()->get('OperationLogs')->addLog(
					$entUser->id,
					\App\Model\Table\OperationTypesTable::ID['WITHDRAWAL'],
					$this->request
				);
				$this->LoginSessionDelete();
				$this->Users->userWithdrawal($entUser);
			}

			$this->Flash->success('ログアウトしました。');
			return $this->redirect('/');
		}

		$this->set('nav', NAV_SETTING);
		$this->set('add_display', false);
		$this->set('search_display', false);
	}
}
