<?php

namespace App\FinancialGoal\Domain;

use App\Shared\Domain\VO\Id;

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
