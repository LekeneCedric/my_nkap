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
        $isUpdate = false;
        $monthlyStatistics = $this->getMonthlyStatisticsOrCreate($command);
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

    private function getMonthlyStatisticsOrCreate(UpdateMonthlyStatisticsCommand $command): MonthlyStatistic
    {
        $monthlyStatistics = $this->repository->ofComposedId($command->composedId);
        if (!$monthlyStatistics) {
            $monthlyStatistics = MonthlyStatistic::create(
                composedId: $command->composedId,
                userId: $command->userId,
                year: $command->year,
                month: $command->month,
            );
        }
        return $monthlyStatistics;
    }
}
