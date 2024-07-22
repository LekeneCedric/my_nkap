<?php

namespace App\Shared\Domain\Enums;

use UnitEnum;

enum MonthEnum: string
{
    case JANUARY = 'january';
    case FEBRUARY = 'february';
    case MARCH = 'march';
    case APRIL = 'april';
    case MAY = 'may';
    case JUNE = 'june';
    case JULY = 'july';
    case AUGUST = 'august';
    case SEPTEMBER = 'september';
    case OCTOBER = 'october';
    case NOVEMBER = 'november';
    case DECEMBER = 'december';

    public static function values(): array
    {
        return array_map(fn(UnitEnum $item) => $item->value, self::cases());
    }
}
