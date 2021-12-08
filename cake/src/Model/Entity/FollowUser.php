<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FollowUser Entity
 *
 * @property int $id
 * @property int $follow_user_id
 * @property int $user_id
 * @property bool $is_deleted
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\FollowUser[] $follow_users
 * @property \App\Model\Entity\User $user
 */
class FollowUser extends Entity
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
        'follow_user_id' => true,
        'user_id' => true,
        'is_deleted' => true,
        'modified' => true,
        'created' => true,
        'follow_users' => true,
        'user' => true,
    ];

}
