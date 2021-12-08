<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * OperationLog Entity
 *
 * @property int $id
 * @property int $type_id
 * @property int $user_id
 * @property string $ip_address
 * @property string $ua
 * @property string $cookie
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\OperationType $operation_type
 * @property \App\Model\Entity\User $user
 */
class OperationLog extends Entity
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
        'type_id' => true,
        'user_id' => true,
        'ip_address' => true,
        'ua' => true,
        'cookie' => true,
        'modified' => true,
        'created' => true,
        'operation_type' => true,
        'user' => true,
    ];
}
