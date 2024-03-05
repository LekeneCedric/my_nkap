<?php

namespace App\FinancialGoal\Domain;

use App\Shared\VO\Id;

interface FinancialGoalRepository
{
    /**
     * @param FinancialGoal $financialGoal
     * @return void
     */
    public function save(FinancialGoal $financialGoal): void;

    /**
     * @param Id $financialGoalId
     * @return FinancialGoal|null
     */
    public function byId(Id $financialGoalId): ?FinancialGoal;
}
