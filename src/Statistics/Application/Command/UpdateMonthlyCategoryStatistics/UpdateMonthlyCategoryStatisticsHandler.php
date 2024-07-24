<?php

namespace App\Statistics\Application\Command\UpdateMonthlyCategoryStatistics;

use App\Statistics\Domain\MonthlyCategoryStatistic;
use App\Statistics\Domain\repositories\MonthlyCategoryStatisticRepository;

class UpdateMonthlyCategoryStatisticsHandler
{
    public function __construct(
        private MonthlyCategoryStatisticRepository $repository,
    )
    {
    }

    public function handle(UpdateMonthlyCategoryStatisticsCommand $command)
    {
        $isUpdate = true;
        $monthlyCategoryStatistic = $this->getMonthlyCategoryStatistic($command);
        if (!$monthlyCategoryStatistic) {
            $isUpdate = false;
            $monthlyCategoryStatistic = MonthlyCategoryStatistic::create(
                composedId: $command->composedId,
                userId: $command->userId,
                year: $command->year,
                month: $command->month,
                categoryId: $command->categoryId,
            );
        }
        if ($command->toDelete) {
            $monthlyCategoryStatistic->updateAfterDeleteOperation(
              previousAmount: $command->previousAmount,
              operationType: $command->operationType,
            );
        }
        if (!$command->toDelete) {
            $monthlyCategoryStatistic->updateAfterSaveOperation(
              previousAmount: $command->previousAmount,
              newAmount: $command->newAmount,
              operationType: $command->operationType,
            );
        }
        if (!$isUpdate)
        {
            $this->repository->create($monthlyCategoryStatistic);
            return;
        }
        $this->repository->update($monthlyCategoryStatistic);
    }

    private function getMonthlyCategoryStatistic(UpdateMonthlyCategoryStatisticsCommand $command): ?MonthlyCategoryStatistic
    {
        return $this->repository->ofComposedId($command->composedId);
    }
}
