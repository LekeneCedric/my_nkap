<?php

namespace App\category\Domain\Enums;

enum CategoryEventStateEnum
{
    case onCreate;
    case onUpdate;
    case onDelete;
}
