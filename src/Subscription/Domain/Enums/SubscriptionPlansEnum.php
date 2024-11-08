<?php

namespace App\Subscription\Domain\Enums;

enum SubscriptionPlansEnum: string
{
    case FREE_PLAN = 'free_plan';
    case STANDARD_PLAN = 'standard_plan';
    case PREMIUM_PLAN = 'premium_plan';

    public static function values(): array
    {
        return array_map(fn($enum) => $enum->value, self::cases());
    }
}
