<?php

namespace App\FinancialGoal\Application\Command;

class MakeFinancialGoalCommand
{
    public ?string $financialGoalId = null;
    public function __construct(
        public string $accountId,
        public string $startDate,
        public string $endDate,
        public float  $desiredAmount,
        public string $details
    )
    {
    }
}
