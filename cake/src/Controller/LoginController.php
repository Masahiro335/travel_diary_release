<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\ORM\Table;
use Cake\Mailer\Mailer;
use Cake\Mailer\MailerAwareTrait;
use Cake\I18n\Time;

/**
 * Login Controller
 *
 * ログイン処理
 */
class LoginController extends AppController
{
	use MailerAwareTrait;
	

  	/**
	 * 登録画面
	 * @author matsubara
	 *
	 * @return 
	 */
	public function signup()
	{
		$this->set('add_display', false);
		$this->set('search_display', false);
	}  


	/**
	 * 新規登録
	 * @author matsubara
	 *
	 * @return 
	 */
	public function registration()
	{
		$user = $this->Users->newEmptyEntity();

		if( empty($this->request->getData()) == false ){
			//仮登録状態の同じメアドのユーザーがいないか確認する
			$targetUser = $this->Users->find()
				->where(['email' => $this->request->getData('email')])
                ->first();

			//仮登録状態いない場合はデータを追加
			if( empty($targetUser) ){
				$registerUser = $this->Users->patchEntity($user, [
					'name' => '名無しさん',
					'email' => $this->request->getData('email'),
					'password' => $this->request->getData('password'),
					'status_id' => \App\Model\Table\UserStatusesTable::ID['TEMPORARY'],
					'register_token' => $this->Users->userUniqueCode(12),
					'token_limit_time' => new Time(REGISTER_LIMIT_TIME),
					'unique_id' => $this->Users->userUniqueCode(8),
				]);
			}else if(
				$targetUser->status_id == \App\Model\Table\UserStatusesTable::ID['TEMPORARY']
				|| $targetUser->status_id == \App\Model\Table\UserStatusesTable::ID['WITHDRAWAL']
			){
				//仮登録ユーザーを更新
				$registerUser = $this->Users->patchEntity($targetUser, [
					'password' => $this->request->getData('password'),
					'status_id' => \App\Model\Table\UserStatusesTable::ID['TEMPORARY'],
					'register_token' => $this->Users->userUniqueCode(12),
					'token_limit_time' => new Time(REGISTER_LIMIT_TIME),
				], ['validate'=>'TmpUserUpdate']);
			}

			if(empty($registerUser) == false && $this->Users->save($registerUser)) {
				//本登録のメール送信
				$this->getMailer('User')->send('register', [$registerUser]);
				$this->Flash->success('仮登録を受付ました。送信された登録メールのリンクをクリックしてください。');

				//ログを追加
				\Cake\ORM\TableRegistry::getTableLocator()->get('OperationLogs')->addLog(
					$registerUser->id,
					\App\Model\Table\OperationTypesTable::ID['TEMPORARY'],
					$this->request
				);
				return $this->redirect('/');	
			}
			//保存の失敗
			$this->Flash->error(
				'入力したメールアドレスは登録できません。' . "\n"
				.'登録できないメールアドレスか。' . "\n"
				. 'または、本登録済みの方はログインをしてください。' . "\n"
			);
		}

		$this->set(compact('user'));
		$this->set('add_display', false);
		$this->set('search_display', false);

	}


	/**
	* トークンで本登録処理
	* @author matsubara
	* @param string $token 本登録用のトークン
	* @return 
	*/
	public function registrationToken(){

		$token = $this->request->getQuery('email');

		$userEntity = $this->Users->find()
			->where([
				'register_token' => $token,
				'status_id' => \App\Model\Table\UserStatusesTable::ID['TEMPORARY'],
				'token_limit_time >' => new Time(),
			])
			->first()
		;

		//トークンに対するユーザーが見つからない場合はエラーセットして会員登録ページへリダイレクト
		if( empty($userEntity) ){
			$this->Flash->error(
				'トークンが無効です。既に本登録済みか、有効期限が切れている可能性があります。' . "\n"
				 . '本登録済みの方は「ログインページ」からログインしてください。' . "\n"
				 . '本登録済みでない方は新規会員登録から登録し直してください。'
			);
			return $this->redirect(['action' => 'registration']);

		//ユーザーが見つかった場合は本登録処理してログインページへリダイレクト
		}else{
			$userEntity->status_id = \App\Model\Table\UserStatusesTable::ID['NORMAL'];
			$userEntity->register_token = '';

			if ( $this->Users->save($userEntity) ) {
				//ログを追加
				\Cake\ORM\TableRegistry::getTableLocator()->get('OperationLogs')->addLog(
					$userEntity->id,
					\App\Model\Table\OperationTypesTable::ID['REGISTRATION'],
					$this->request
				);
				$this->Flash->success('登録完了しました。ログインしてください。');
				return $this->redirect(['action' => 'signin']);
			}
		}

		//最終的にリダイレクトのリターンしていない場合はエラーとして会員登録ページへリダイレクト
		$this->Flash->error('会員の本登録に失敗しました。お手数ですが再度会員登録をお試しください。');
		return $this->redirect(['action' => 'registration']);
	}

	/**
	 * ログイン
	 * @author matsubara
	 *
	 * @return 
	 */
	public function signin($sns_provider_id = null)
	{
		$this->set('add_display', false);
		$this->set('search_display', false);

		if(empty($this->request->getQuery('denied')) == false) return $this->redirect(['action' => 'signin']);

		if( !empty($this->request->getData()) || empty($sns_provider_id) == false ){

			//SNSログイン、SNSサインアップ
			if( empty($sns_provider_id) == false ){
				$aryResSnsLogin = $this->Users->SnsLogin( $sns_provider_id, SYSTEM_URL.'signin/'.$sns_provider_id );

				$this->SnsTokens = \Cake\ORM\TableRegistry::getTableLocator()->get('SnsTokens');

				switch($aryResSnsLogin['result']){
					//OAuth認証失敗した場合は、リダイレクトする
					case \App\Model\Table\UsersTable::SNS_LOGIN_REDIRECT:
						return $this->redirect('/signup');

					//Snsトークンからユーザーを取得できない場合、新規登録
					case \App\Model\Table\UsersTable::SNS_LOGIN_UNKNOWN_TOKEN:
						if( empty($aryResSnsLogin['snsProfile']->email) == false ){

							// ソーシャルメアドレス (snsProfile.email) と sns.tokenのメールアドレス（sns_tokens.email）が一致の場合：トークンを更新
							$snsTokenEntity = $this->SnsTokens->find()
								->where(['email' => $aryResSnsLogin['snsProfile']->email])
								->first()
							;
							if( empty($snsTokenEntity) == false ){
								$snsTokenEntity->token = $aryResSnsLogin['snsProfile']->identifier;
								$this->SnsTokens->save($snsTokenEntity);
							}else{
								$entUser = $this->Users->find()
									->where(['email' => $aryResSnsLogin['snsProfile']->email])
									->first();
								if( empty($entUser) == false ) return $this->Flash->error('このメールアドレスは既に登録されています。' );

								try{
									//新規登録
									$newUser = $this->Users->newEntity([
										'name' => $aryResSnsLogin['snsProfile']->displayName,
										'email' => $aryResSnsLogin['snsProfile']->email,
										'password' => $this->Users->userUniqueCode(8),
										'status_id' =>  \App\Model\Table\UserStatusesTable::ID['NORMAL'],
										'unique_id' => $this->Users->userUniqueCode(8),
									]);
									if($this->Users->save($newUser) == false) throw new Exception();
								
									$SnsToken = $this->SnsTokens->newEntity([
											'sns_token' => $aryResSnsLogin['snsProfile']->identifier,
											'user_id' => $newUser->id,
											'sns_provider_id' => $sns_provider_id,
											'email' => $aryResSnsLogin['snsProfile']->email,
										]);
									if($this->SnsTokens->save($SnsToken) == false) throw new Exception();

									//ユーザー情報をセッションに書き込み
									$this->LoginSession($newUser);
	
									$this->Flash->success('新規登録に成功しました。');
									return $this->redirect("/mypage/users/profile/{$newUser->id}");
								}catch(Exception $e){
									$this->Flash->success('新規登録に失敗しました。');
									return $this->redirect('/signup');
								}
							}
						}else{											
							$this->Flash->error('外部サービスの認証でメールアドレスの取得（リクエスト）を許可した上で認証してください。');
							//ユーザーがSNSログインをし直した際にセッションにキャッシュしたログイン情報を使わないようにSNS認証情報をクリアする
							\Cake\ORM\TableRegistry::getTableLocator()->get('SnsProviders')->RecentlyAdapterDisconnect();
							return;
						}

					//Snsトークンからユーザーを取得した場合、ログイン
					case \App\Model\Table\UsersTable::SNS_LOGIN_SUCCESS:

						if( empty($aryResSnsLogin['entUser']) ){
							$this->Flash->error('ログイン情報が取得できませんでした。');
							$this->Flash->error('既に同じメールアドレスが登録されている可能性があります。');
							return $this->redirect(['action' => 'signin']);
						}

						$entUser = $aryResSnsLogin['entUser'];

						$snsTokenEntity = $this->SnsTokens->find()
							->where(['user_id' => $entUser->id])
							->first();

						// SNSとメールアドレスが異なる場合はSNSのメールアドレスで情報を更新
						if( empty($aryResSnsLogin['snsProfile']->email) == false && $aryResSnsLogin['snsProfile']->email != $snsTokenEntity->email ){

							// Validation : メールアドレスはすでに登録してあるかどうか
							$tmpUserEntity = $this->Users->find()
								->where([
									'email' => $aryResSnsLogin['snsProfile']->email,
									'id !=' => $entUser->id,
								])
								->first()
							;

							if( empty($tmpUserEntity) == false ){
								$this->Flash->error('このメールアドレスは既に登録されています。' );
								return $this->redirect('/signup');
							} 

							$snsTokenEntity->email = $aryResSnsLogin['snsProfile']->email;

							if( $this->SnsTokens->save($snsTokenEntity) ){
								$this->Flash->success('外部サービスでメールアドレスの変更を確認しましたので、メールアドレスが更新されました。');
							}else{
								return $this->redirect('/signup');
							}
						}

						if(
							$entUser->status_id == \App\Model\Table\UserStatusesTable::ID['WITHDRAWAL']
							|| $entUser->status_id == \App\Model\Table\UserStatusesTable::ID['FORCED_WITHDRAWAL']
							|| $entUser->is_deleted == true
						){
							$this->Flash->error('このユーザーはログインできません。' );
							return $this->redirect('/');
						}

						//ログインに成功
						$this->LoginSession($entUser);
						$this->Flash->success('ログインに成功しました。');
						return $this->redirect('/');
				}
			}

			if(empty($this->request->getData('email')) || empty($this->request->getData('password'))){
				return $this->Flash->error('メールアドレスまたはパスワードが未記入です。');
			}

			$entUser = $this->Users->find()
				->where(['email' => $this->request->getData('email') ])
				->first()
				;

			//ユーザーログインのチェック
			if(empty($entUser) || password_verify($this->request->getData('password'), $entUser->password) == false){
				return $this->Flash->error('メールアドレスまたはパスワードが違います。');

			}else if(
				$entUser->status_id == \App\Model\Table\UserStatusesTable::ID['WITHDRAWAL']
				|| $entUser->status_id == \App\Model\Table\UserStatusesTable::ID['FORCED_WITHDRAWAL']
				|| $entUser->is_deleted == true
			){
				return $this->Flash->error('このユーザーはログインできません。');

			}else if($entUser->status_id == \App\Model\Table\UserStatusesTable::ID['TEMPORARY']){
				//本登録トークンの有効期限を延長して本登録メールを再送
				$entUser->token_limit_time = new Time(REGISTER_LIMIT_TIME);
				//もし本登録トークンが無ければ作成
				if( empty($entUser->register_token) ) $entUser->register_token = $this->Users->userUniqueCode(12);

				if ($this->Users->save($entUser)) {
					//本登録のメール送信
					$this->getMailer('User')->send('register', [$entUser]);
					return $this->Flash->error('仮登録中のユーザーです。再送された仮登録メールのリンクをクリックしてください。');
				}

				return $this->Flash->error('ログインに失敗しました。');
			}

			//ユーザー情報をセッションに書き込み
			$this->LoginSession($entUser);

			$this->OperationLogs = \Cake\ORM\TableRegistry::getTableLocator()->get('OperationLogs');

			//ユーザーログをチェック
			$userOperationLogs = $this->OperationLogs->find()
				->where([
					'user_id' => $entUser->id,
					'type_id' => \App\Model\Table\OperationTypesTable::ID['SIGNIN'],
				])
				->first();

			//初ログイン
			if(empty($userOperationLogs)){
				$first_signin = true;
			}

			//ログを追加
			$this->OperationLogs ->addLog(
				$entUser->id,
				\App\Model\Table\OperationTypesTable::ID['SIGNIN'],
				$this->request
			);
		
			if( empty($first_signin) ){
				$this->Flash->success('ログインしました。ようこそ！');
				return $this->redirect('/');
			}

			$this->Flash->success('ログインしました。プロフィールを作成して下さい');
			return $this->redirect('/mypage/users/profile/'.$entUser->id);

		}
	}


	/**
	 * ログアウト
	 * @author matsubara
	 *
	 * @return 
	 */
	public function signout()
	{
		if( empty($this->Auth) ){
			$this->Flash->error('ログインをしていないためログアウトできません');
			return $this->redirect('/');
		}

		//ログを追加
		\Cake\ORM\TableRegistry::getTableLocator()->get('OperationLogs')->addLog(
			$this->Auth->id,
			\App\Model\Table\OperationTypesTable::ID['SIGNOUT'],
			$this->request
		);
		$this->LoginSessionDelete();
		//ログアウト成功
		$this->Flash->success('ログアウトしました。');
		return $this->redirect('/');
	}

	/**
	 * メールアドレスの変更　再設定
	 * @author matsubara
	 *
	 * @return 
	 */
	public function emailReset()
	{
		$this->autoRender = false;

		$encrypt_data = $this->request->getQuery('reset');
		if( empty($encrypt_data) ){
			$this->Flash->error('データの取得に失敗しました。');
			return $this->redirect('/');
		}


		$array_data = $this->Users->sslDecrypt( $encrypt_data );

		$entUser = $this->Users->find()
			->where([
				'id' => $array_data->id,
				'tmp_mail_token' => $array_data->token,
				'token_limit_time >' => new Time(),
			])
			->first()
			;
		if( empty($entUser) ){
			$this->Flash->error('ユーザーの取得に失敗しました。');
			return $this->redirect('/');
		}

		$entUser = $this->Users->patchEntity(
			$entUser, 
			['email' => $array_data->email], 
			['validate'=>'EmailEdit']
		);

		if( $this->Users->save($entUser) ){
			$this->Flash->success('メールアドレスの変更に成功しました。');
			return $this->redirect('/');
		}

		$this->Flash->error('メールアドレスの変更に失敗しました。');
		return $this->redirect('/');
	}

	/**
	 * パスワードの変更　再設定
	 * @author matsubara
	 *
	 * @return 
	 */
	public function passwordReset()
	{
		if( empty($this->request->getData()) == false ){
			$email = $this->request->getData('email');
			$entUser = $this->Users->find()->where(['email' => $email])->first();
			$code = $this->Users->generateCode(4);

			if( empty($entUser) || empty($code) ){
				$this->Flash->error('メール送信に失敗しました。');
				return $this->redirect(['action' => 'passwordReset']);
			}
			$entUser->auth_code = $code;
			if( $this->Users->save($entUser) ){
				//メール送信
				$this->getMailer('User')->send('passwordReset', [$email, $code]);
				//セッションにメールアドレスを書き込み
				$this->Session->write('PasswordResetEmail', $entUser->email);
				$this->redirect(['action' => 'passwordResetCode']);
			}
		}
		$this->set('add_display', false);
		$this->set('search_display', false);
		$this->set('map_display', false);
	}

	/**
	* パスワードの変更　コード認証
	* @author matsubara
	*
	* @return 
	*/
   public function passwordResetCode()
   {
	   	//セッションチェック。(メールアドレスが送信されている状態か確認)
		if( $this->Session->check('PasswordResetEmail') == false ){
			$this->Flash->error('認証コードのメールを送信してください。');
			return $this->redirect(['action' => 'passwordReset']);
		}
	
		if( empty($this->request->getData()) == false ){	
			$code = $this->request->getData('code');

			if( empty($code) ){
				$this->Flash->error('認証に失敗しました。');
				return $this->redirect(['action' => 'passwordResetCode']);
			}

			$entUser = $this->Users->find()
				->where([
					'auth_code' => $code,
					'email' => $this->Session->read('PasswordResetEmail'),
				])
				->first();

			if(empty($entUser) == false){
				$this->Flash->success('認証に成功しました。');
				return $this->redirect(['action' => 'passwordResetComplete']);
			}
			$this->Flash->error('認証に失敗しました。');
			return $this->redirect(['action' => 'passwordResetCode']);
		}	
   }

   	/**
	* パスワードの変更　完了
	* @author matsubara
	*
	* @return 
	*/
	public function passwordResetComplete()
	{
		//セッションチェック。(メールアドレスが送信されている状態か確認)
		 if( $this->Session->check('PasswordResetEmail') == false ){
			 $this->Flash->error('認証コードのメールを送信してください。');
			 return $this->redirect(['action' => 'passwordReset']);
		 }
	 
		 if( empty($this->request->getData()) == false ){
				$password = $this->request->getData('password');
				if( empty($password) ){
					$this->Flash->error('パスワードの取得に失敗しました。');
					return $this->redirect(['action' => 'passwordReset']);
				}

				$entUser = $this->Users->find()
					->where(['email' => $this->Session->read('PasswordResetEmail')])
					->first()
					;
	
				$entUser = $this->Users->patchEntity(
					$entUser,
					['password' => $password],
					['validate'=>'PasswordEdit']
				);

				if( $this->Users->save($entUser) ){
					//セッションの削除。
					$this->Session->delete('PasswordResetEmail');
					$this->Flash->success('パスワードの変更に成功しました。');
					return $this->redirect(['action' => 'signin']);
				}
			$this->Flash->error('パスワードの変更に失敗しました。');
			return $this->redirect(['action' => 'passwordReset']);
		 }	
	}
}
