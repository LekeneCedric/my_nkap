<?php

namespace App\Shared\Domain\Enums;

use UnitEnum;

enum MonthEnum: int
{
    case JANUARY = 1;
    case FEBRUARY = 2;
    case MARCH = 3;
    case APRIL = 4;
    case MAY = 5;
    case JUNE = 6;
    case JULY = 7;
    case AUGUST = 8;
    case SEPTEMBER = 9;
    case OCTOBER = 10;
    case NOVEMBER = 11;
    case DECEMBER = 12;

    public static function values(): array
    {
        return array_map(fn(UnitEnum $item) => $item->value, self::cases());
    }
}
