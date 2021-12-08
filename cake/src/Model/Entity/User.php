<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;
use App\Model\Entity\Traits\AssetUserTrait;
use Cake\ORM\TableRegistry;

/**
 * User Entity
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $status_id
 * @property int|null $prefecture_id
 * @property string|null $profile
 * @property string $unique_id
 * @property bool $is_icon
 * @property \Cake\I18n\FrozenTime|null $icon_upload_datetime
 * @property string $icon_extension 
 * @property bool $is_home
 * @property \Cake\I18n\FrozenTime|null $home_upload_datetime
 * @property string $home_extension 
 * @property string|null $register_token
 * @property \Cake\I18n\FrozenTime|null $token_limit_time
 * @property bool $is_deleted
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\UserStatus $user_status
 * @property \App\Model\Entity\Prefecture $prefecture
 * @property \App\Model\Entity\Unique $unique
 * @property \App\Model\Entity\Comment[] $comments
 * @property \App\Model\Entity\FollowUser[] $follow_users
 * @property \App\Model\Entity\Good[] $goods
 * @property \App\Model\Entity\MessageImage[] $message_images
 * @property \App\Model\Entity\Message[] $messages
 * @property \App\Model\Entity\OperationLog[] $operation_logs
 */
class User extends Entity
{
    // 画像についての設定
	use AssetUserTrait;
	const ASSET_FOLDER_NAME = 'users';

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'email' => true,
        'password' => true,
        'status_id' => true,
        'prefecture_id' => true,
        'profile' => true,
        'unique_id' => true,
        'is_icon' => true,
        'icon_upload_datetime' => true,
        'icon_extension' => true,
        'is_home' => true,
        'home_upload_datetime' => true,
        'home_extension' => true,
        'register_token' => true,
        'token_limit_time' => true,
        'is_deleted' => true,
        'modified' => true,
        'created' => true,
        'user_status' => true,
        'prefecture' => true,
        'unique' => true,
        'comments' => true,
        'follow_users' => true,
        'goods' => true,
        'message_images' => true,
        'messages' => true,
        'operation_logs' => true,
    ];


    /**
	 * パスワードをハッシュして保存
     * 
	 * 以下の文献を参考。PASSWORD_DEFAULT方式のハッシュは、
	 * PHPに実装された一番強力な方式でハッシュする。
	 * また、ソルトやハッシュ方式も一緒にreturnされるので、
	 * PHPのデフォルトが変更されてもちゃんと認識できる。
	 * 
	 * ▼安全なパスワードハッシュ
	 * https://www.php.net/manual/ja/faq.passwords.php
	 * ▼パスワードハッシュ関数
	 * https://www.php.net/manual/ja/function.password-hash.php
     * 
	 * @param string $password
     * @return string|null
     */ 
    protected function _setPassword($password)
	{
		if( $password === '' ){
			return '';
		}elseif (strlen($password) > 0) {
			return password_hash($password, PASSWORD_DEFAULT);
		}
		return null;
	}

    /**
     * ログインユーザーがフォローしたユーザーの判断
     * @param array entity ログインユーザー
     * @return bool ログイン済み か　なし
     */
    public function AuthfollowUser($Auth = null)
    {
        if(empty($Auth)) return false;

        $followUser = \Cake\ORM\TableRegistry::getTableLocator()->get('FollowUsers')->find()
            ->where([
                'user_id' => $Auth->id,
                'follow_user_id' => $this->id,
                'is_deleted' => false,
            ])
            ->first()
            ;
        if(empty($followUser)) return false;

        return true;

    }

    /**
	 * ユーザーがフォローしたユーザーを取得
	 * @return query ユーザーがフォローしたユーザー
	 */
    public function followUsers()
	{
        $followUsersIds = \Cake\ORM\TableRegistry::getTableLocator()->get('FollowUsers')->find()
            ->select('follow_user_id')
            ->where([
                'user_id' => $this->id,
                'is_deleted' => false,
            ]);
		$queryfollowUsers = \Cake\ORM\TableRegistry::getTableLocator()->get('Users')
            ->queryDefalut()
			->where(['Users.id IN' => $followUsersIds]);

        return $queryfollowUsers;
	}

	/**
	 * ユーザーをフォローしたユーザーを取得
	 * @param array entity ログインユーザー
	 * @return query ユーザーをフォローしたユーザー
	 */
    public function followerUsers()
	{
        $followerUsersIds = \Cake\ORM\TableRegistry::getTableLocator()->get('FollowUsers')->find()
            ->select('user_id')
            ->where([
                'follow_user_id' => $this->id,
                'is_deleted' => false,
            ]);
		$queryfollowerUsers = \Cake\ORM\TableRegistry::getTableLocator()->get('Users')
            ->queryDefalut()
			->where(['Users.id IN' => $followerUsersIds]);

        return $queryfollowerUsers;
	}

}
