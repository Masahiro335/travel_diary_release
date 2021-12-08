<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\Traits\AssetTrait;
use Cake\ORM\TableRegistry;

/**
 * MessageImage Entity
 *
 * @property int $id
 * @property int $message_id
 * @property int $user_id
 * @property int $sort
 * @property \Cake\I18n\FrozenTime|null $image_upload_datetime
 * @property string $image_extension
 * @property bool $is_deleted
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\Message $message
 * @property \App\Model\Entity\User $user
 */
class MessageImage extends Entity
{
    // 画像についての設定
	use AssetTrait;
	const ASSET_FOLDER_NAME = 'messages';

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
        'message_id' => true,
        'user_id' => true,
        'sort' => true,
        'image_upload_datetime' => true,
        'image_extension' => true,
        'is_deleted' => true,
        'modified' => true,
        'created' => true,
        'message' => true,
        'user' => true,
    ];
}
