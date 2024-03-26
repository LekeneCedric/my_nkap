<?php

namespace App\FinancialGoal\Application\Command\Make;

class MakeFinancialGoalResponse
{
    public ?string $financialGoalId = null;
    public ?string $message = null;
    public ?string $createdAt = null;
    public function __construct(
        public bool $status,
        public bool $isMake,
    )
    {
    }
}
