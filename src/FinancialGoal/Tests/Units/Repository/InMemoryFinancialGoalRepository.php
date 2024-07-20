<?php

namespace App\FinancialGoal\Tests\Units\Repository;

use App\FinancialGoal\Domain\FinancialGoal;
use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\Shared\VO\Id;

class InMemoryFinancialGoalRepository implements FinancialGoalRepository
{
    /**
     * @var FinancialGoal[]
     */
    public array $financialsGoal = [];

    /**
     * @param FinancialGoal $financialGoal
     * @return void
     */
    public function save(FinancialGoal $financialGoal): void
    {
       $this->financialsGoal[$financialGoal->id()->value()] = $financialGoal;
    }

    /**
     * @param Id $financialGoalId
     * @return FinancialGoal|null
     */
    public function byId(Id $financialGoalId): ?FinancialGoal
    {
        if (!array_key_exists($financialGoalId->value(), $this->financialsGoal)) {
            return null;
        }
        return $this->financialsGoal[$financialGoalId->value()];
    }

    public function ofsAccountId(string $accountId): array
    {
        return [];
    }

    public function updateMany(array $financialGoals): void
    {
        //
    }
}
