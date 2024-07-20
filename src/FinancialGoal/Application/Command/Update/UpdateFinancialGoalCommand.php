<?php

namespace App\FinancialGoal\Application\Command\Update;

class UpdateFinancialGoalCommand
{
    public function __construct(
        public string $accountId,
        public float  $previousAmount,
        public float  $amount,
        public string $operationDate,
    )
    {
    }
}
