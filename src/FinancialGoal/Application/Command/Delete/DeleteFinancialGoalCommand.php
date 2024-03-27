<?php

namespace App\FinancialGoal\Application\Command\Delete;

class DeleteFinancialGoalCommand
{
    public function __construct(
        public string $financialGoalId,
    )
    {
    }
}
