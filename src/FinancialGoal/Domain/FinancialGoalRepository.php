<?php

namespace App\FinancialGoal\Domain;

use App\FinancialGoal\Domain\Exceptions\ErrorOnSaveFinancialGoalException;
use App\Shared\VO\Id;

interface FinancialGoalRepository
{
    /**
     * @param FinancialGoal $financialGoal
     * @return void
     * @throws ErrorOnSaveFinancialGoalException
     */
    public function save(FinancialGoal $financialGoal): void;

    /**
     * @param Id $financialGoalId
     * @return FinancialGoal|null
     */
    public function byId(Id $financialGoalId): ?FinancialGoal;

    /**
     * @param string $accountId
     * @return FinancialGoal[]
     */
    public function ofsAccountId(string $accountId): array;

    /**
     * @param array $financialGoals
     * @return void
     */
    public function updateMany(array $financialGoals): void;
}
