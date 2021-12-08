<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Message Entity
 *
 * @property int $id
 * @property string $message
 * @property int $user_id
 * @property bool $is_edit
 * @property bool $is_deleted
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Comment[] $comments
 * @property \App\Model\Entity\Good[] $goods
 * @property \App\Model\Entity\MessageImage[] $message_images
 * @property \App\Model\Entity\PrefectureMessage[] $prefecture_messages
 */
class Message extends Entity
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
        'message' => true,
        'user_id' => true,
        'is_edit' => true,
        'is_deleted' => true,
        'modified' => true,
        'created' => true,
        'comments' => true,
        'goods' => true,
        'message_images' => true,
        'prefecture_messages' => true,
        'prefectures' => true,
    ];

    //都道府県を配列に変換
    public function prefecturesList($queryPrefectures = null){
        foreach($queryPrefectures as $entPrefecture){
            $Prefectureslist[] = $entPrefecture->id;
        }
        return $Prefectureslist;
    }
}
