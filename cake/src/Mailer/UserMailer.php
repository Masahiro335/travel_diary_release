<?php
namespace App\Mailer;
 
use Cake\Mailer\Mailer;
 
class UserMailer extends Mailer
{
	/**
	 * 仮登録の登録
	 * @author matsubara
	 * @param entity ユーザーデータ
	 * @return 
	 */
	public function register($user)
	{
		$this
            ->setFrom([MAIL_FROM_ADDRESS  => SYSTEM_NAME])	// 送り元
			->setTo($user->email)	// 宛先の設定
			->setSubject('本登録へのご案内')	// 件名の設定
            ->viewBuilder()
                ->setTemplate('user_registration')  // テンプレートの設定
                ->setVar('token',$user->register_token)    // テンプレートに渡す変数
            ;	
	}


	/**
	 * メールアドレスの変更
	 * @author matsubara
	 *
	 * @return 
	 */
	public function emailEdit($email, $urlEncoded)
	{
		$this
            ->setFrom([MAIL_FROM_ADDRESS  => SYSTEM_NAME])	// 送り元
			->setTo($email)										// 宛先の設定
			->setSubject('メールアドレスの変更')					// 件名の設定
            ->viewBuilder()
                ->setTemplate('user_email_edit')  			// テンプレートの設定
                ->setVar('reset',$urlEncoded)    		// テンプレートに渡す変数
            ;	
	}


	/**
	 * メールアドレスの変更
	 * @author matsubara
	 *
	 * @return 
	 */
	public function passwordReset($email, $code)
	{
		$this
            ->setFrom([MAIL_FROM_ADDRESS  => SYSTEM_NAME])	// 送り元
			->setTo($email)										// 宛先の設定
			->setSubject('パスワードの再設定')					// 件名の設定
            ->viewBuilder()
                ->setTemplate('user_password_reset')  			// テンプレートの設定
                ->setVar('code',$code)    		// テンプレートに渡す変数
            ;	
	}
}