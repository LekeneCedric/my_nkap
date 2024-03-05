<?php

namespace App\FinancialGoal\Application\Command;

class MakeFinancialGoalResponse
{
    public ?string $financialGoalId = null;
    public ?string $message = null;
    public function __construct(
        public bool $isMake,
    )
    {
    }
}
