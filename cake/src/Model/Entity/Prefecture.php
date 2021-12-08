<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Prefecture Entity
 *
 * @property int $id
 * @property string $name
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\PrefectureMessage[] $prefecture_messages
 * @property \App\Model\Entity\User[] $users
 */
class Prefecture extends Entity
{
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
        'modified' => true,
        'created' => true,
        'prefecture_messages' => true,
        'users' => true,
        'messages' => true,
    ];

    /**
	 * ユーザーの旅行先の投稿数を取得
	 * @author matsubara
	 * @param array ユーザーが行った旅行先の投稿数
	 * Ajaxに出力
	 * @return  int number 旅行先の投稿数
	 */
	public function userPrefecturesCount($arrayPrefectureIds = null)
	{
		if(empty($arrayPrefectureIds) || empty( array_count_values($arrayPrefectureIds)[$this->id] )) return 0;

         return array_count_values($arrayPrefectureIds)[$this->id];
	}

    /**
	 * 期間内に投稿された数を出力
	 * @author matsubara
	 * @param date $month 月
     * @param bool $is_follow_check フォローユーザーのみのチェック
     * @param　Entity ログイン情報
	 * Ajaxに出力
	 * @return int $monthPrefecturesCount 旅行先の投稿数
	 */
	public function monthPrefecturesCount($month, $is_follow_check, $Auth = null)
	{
        //フォローユーザーのみ
        $FollowUserIds = '';
        if( empty($is_follow_check) == false && empty($Auth) == false){
            $this->FollowUsers = \Cake\ORM\TableRegistry::getTableLocator()->get('FollowUsers');
            $FollowUserIds = $this->FollowUsers->find()
                ->SELECT('follow_user_id')
                ->where([
                    'user_id' => $Auth->id,
                    'is_deleted' => false,
                ]);
        }

        $tblPrefectureMessages = \Cake\ORM\TableRegistry::getTableLocator()->get('PrefectureMessages');
        $monthPrefecturesCount = $tblPrefectureMessages->find()
            ->contain('Messages.Users')
            ->where([
                'PrefectureMessages.prefecture_id' => $this->id,
                'Messages.is_deleted' => false,
                'Messages.created >=' => date('Y-m-d', strtotime('first day of'.($month.' 00:00:00'))),
                'Messages.created <=' => date('Y-m-d', strtotime('last day of'.($month.' 23:59:59.999999'))),
                'Users.is_deleted' => false,
                empty($FollowUserIds) ? '' : ['Messages.user_id IN' => $FollowUserIds]
            ])
            ->count()
            ;
        if(empty($monthPrefecturesCount)) return 0;

        return $monthPrefecturesCount;
	}
}
