<?php

namespace App\FinancialGoal\Domain\Enum;

enum ComparisonEnum: int
{
    case GREATER = 1;
    case LESS = -1;
    case EQUAL = 0;
}
