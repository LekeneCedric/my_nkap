<?php

namespace App\FinancialGoal;

use App\Shared\VO\Id;

class FinancialGoalUser
{
    public function __construct(
        private Id $userId
    )
    {
    }

    public function id(): Id
    {
        return $this->userId;
    }
}
