<?php

namespace App\Shared\Infrastructure\Enums;

enum ErrorLevelEnum: string
{
    case WARNING = 'warning';
    case CRITICAL = 'critical';
    case INFO = 'info';
}
