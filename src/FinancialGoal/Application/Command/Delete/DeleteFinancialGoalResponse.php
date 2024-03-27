<?php

namespace App\FinancialGoal\Application\Command\Delete;

class DeleteFinancialGoalResponse
{

    /**
     * @param bool $status
     * @param bool $isDeleted
     */
    public function __construct(
        public bool $status,
        public bool $isDeleted,
    )
    {
    }
}
