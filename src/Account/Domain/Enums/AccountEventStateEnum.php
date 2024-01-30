<?php

namespace App\Account\Domain\Enums;

enum AccountEventStateEnum
{
    case onCreate;
    case onUpdate;
    case onDelete;
}
