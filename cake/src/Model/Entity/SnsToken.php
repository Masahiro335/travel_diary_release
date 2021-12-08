<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SnsToken Entity
 *
 * @property int $id
 * @property string $sns_token
 * @property int $user_id
 * @property int $sns_provider_id
 * @property string $email
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\SnsProvider $sns_provider
 */
class SnsToken extends Entity
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
        'sns_token' => true,
        'user_id' => true,
        'sns_provider_id' => true,
        'email' => true,
        'modified' => true,
        'created' => true,
        'user' => true,
        'sns_provider' => true,
    ];
}
