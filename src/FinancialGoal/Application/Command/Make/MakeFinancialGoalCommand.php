<?php

namespace App\FinancialGoal\Application\Command\Make;

class MakeFinancialGoalCommand
{
    public ?string $financialGoalId = null;
    public function __construct(
        public string $userId,
        public string $accountId,
        public string $startDate,
        public string $endDate,
        public float  $desiredAmount,
        public string $details
    )
    {
    }
}
