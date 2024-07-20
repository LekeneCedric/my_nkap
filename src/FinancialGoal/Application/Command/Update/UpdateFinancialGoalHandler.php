<?php

namespace App\FinancialGoal\Application\Command\Update;

use App\FinancialGoal\Domain\Enum\ComparisonEnum;
use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\Shared\VO\DateVO;
use Exception;

class UpdateFinancialGoalHandler
{
    public function __construct(
        private FinancialGoalRepository $repository,
    )
    {
    }

    /**
     * @param UpdateFinancialGoalCommand $command
     * @return void
     * @throws Exception
     */
    public function handle(UpdateFinancialGoalCommand $command): void
    {
        $financialGoals = $this->repository->ofsAccountId($command->accountId);

        foreach ($financialGoals as $financialGoal) {
            if ($command->previousAmount > 0) {
                $operationDate = new DateVO($command->operationDate);
                if ($operationDate->compare($financialGoal->startDate()) == ComparisonEnum::LESS ) {
                    $financialGoal->retrieveAmount($command->previousAmount);
                }
            }
            $financialGoal->addAmount($command->amount);
        }
        $this->repository->updateMany($financialGoals);
    }
}
