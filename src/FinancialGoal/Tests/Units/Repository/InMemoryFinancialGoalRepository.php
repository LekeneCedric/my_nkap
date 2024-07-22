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
    public array $financialsGoals = [];

    /**
     * @param FinancialGoal $financialGoal
     * @return void
     */
    public function save(FinancialGoal $financialGoal): void
    {
       $this->financialsGoals[$financialGoal->id()->value()] = $financialGoal;
    }

    /**
     * @param Id $financialGoalId
     * @return FinancialGoal|null
     */
    public function byId(Id $financialGoalId): ?FinancialGoal
    {
        if (!array_key_exists($financialGoalId->value(), $this->financialsGoals)) {
            return null;
        }
        return $this->financialsGoals[$financialGoalId->value()];
    }

    public function ofsAccountId(string $accountId): array
    {
        $financialGoals = [];
        foreach ($this->financialsGoals as $financialGoal) {
            if ($financialGoal->toDto()->accountId === $accountId) {
                $financialGoals[] = $financialGoal;
            }
        }
        return $financialGoals;
    }

    /**
     * @param FinancialGoal[] $financialGoals
     * @return void
     */
    public function updateMany(array $financialGoals): void
    {
        foreach ($financialGoals as $financialGoal) {
            $this->financialsGoals[$financialGoal->toDto()->id] = $financialGoal;
        }
    }
}
