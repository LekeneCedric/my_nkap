<?php

namespace App\User\Domain\Enums;

use UnitEnum;

enum UserStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';

    static function values() {
        return array_map(fn(UnitEnum $item) => $item->value , self::cases());
    }
}
