<?php

namespace App\Statistics\Application\Command\UpdateMonthlyStatistics;

use App\Statistics\Domain\MonthlyStatistic;
use App\Statistics\Domain\repositories\MonthlyStatisticRepository;

class UpdateMonthlyStatisticsHandler
{
    public function __construct(
        private MonthlyStatisticRepository $repository,
    )
    {
    }

    public function handle(UpdateMonthlyStatisticsCommand $command): void
    {
        $isUpdate = true;
        $monthlyStatistics = $this->getMonthlyStatistics($command);
        if (!$monthlyStatistics) {
            $isUpdate = false;
            $monthlyStatistics = MonthlyStatistic::create(
                composedId: $command->composedId,
                userId: $command->userId,
                year: $command->year,
                month: $command->month,
            );
        }
        if ($command->toDelete) {
            $monthlyStatistics->updateAfterDeleteOperation(
                previousAmount: $command->previousAmount,
                operationType: $command->operationType,
            );
        }
        if (!$command->toDelete) {
            $monthlyStatistics->updateAfterSaveOperation(
                previousAmount: $command->previousAmount,
                newAmount: $command->newAmount,
                operationType: $command->operationType
            );
        }
        if (!$isUpdate) {
            $this->repository->create($monthlyStatistics);
            return;
        }
        $this->repository->update($monthlyStatistics);
    }

    private function getMonthlyStatistics(UpdateMonthlyStatisticsCommand $command): ?MonthlyStatistic
    {
        return $this->repository->ofComposedId($command->composedId);
    }
}
