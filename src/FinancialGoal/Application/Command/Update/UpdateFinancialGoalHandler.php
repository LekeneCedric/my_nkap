<?php

namespace App\FinancialGoal\Application\Command\Update;

use App\FinancialGoal\Domain\Enum\ComparisonEnum;
use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\VO\DateVO;
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
            $operationDate = new DateVO($command->operationDate);
            if ($command->isDelete) {
                if ($command->type === OperationTypeEnum::INCOME) {
                    $financialGoal->retrieveAmount($command->previousAmount);
                }
                if ($command->type === OperationTypeEnum::EXPENSE) {
                    $financialGoal->addAmount($command->previousAmount);
                }
            }
            if (!$command->isDelete) {
                $operationDateIsInFinancialGoalInterval =
                    !($operationDate->compare($financialGoal->startDate()) === ComparisonEnum::LESS->value) &&
                    !($operationDate->compare($financialGoal->endDate()) === ComparisonEnum::GREATER->value);
                if ($operationDateIsInFinancialGoalInterval) {
                    $isUpdate = $command->previousAmount > 0;
                    if (!$isUpdate) {
                        if ($command->type === OperationTypeEnum::INCOME) {
                            $financialGoal->addAmount($command->amount);
                        }
                        if ($command->type === OperationTypeEnum::EXPENSE) {
                            $financialGoal->retrieveAmount($command->previousAmount);
                        }
                        continue;
                    }
                    if ($command->type === OperationTypeEnum::INCOME) {
                        $financialGoal->retrieveAmount($command->previousAmount);
                        $financialGoal->addAmount($command->amount);
                    }
                    if ($command->type === OperationTypeEnum::EXPENSE) {
                        $financialGoal->addAmount($command->previousAmount);
                        $financialGoal->retrieveAmount($command->amount);
                    }
                }
            }
        }
        $this->repository->updateMany($financialGoals);
    }
}
