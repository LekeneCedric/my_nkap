<?php

namespace App\FinancialGoal\Domain\Enum;

enum FinancialGoalEventStateEnum
{
    case onCreate;
    case onDelete;
    case onUpdate;
}
