<?php

namespace App\Shared\Infrastructure\Logs\Enum;

enum LogLevelEnum: string
{
    case NOTICE = 'notice';
    case INFO = 'info';
    case WARNING = 'warning';
    case ERROR = 'error';
    case CRITICAL = 'critical';
    case ALERT = 'alert';
    case EMERGENCY = 'emergency';

    public static function values(): array
    {
        return array_map(fn($enum) => $enum->value, self::cases());
    }
}
