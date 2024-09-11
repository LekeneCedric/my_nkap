<?php

namespace App\Statistics\Domain\Subscribers;

use App\Operation\Domain\Events\OperationDeleted;
use App\Operation\Domain\Events\OperationSaved;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventSubscriber;
use App\Statistics\Application\Command\UpdateMonthlyCategoryStatistics\UpdateMonthlyCategoryStatisticsCommand;
use App\Statistics\Application\Command\UpdateMonthlyCategoryStatistics\UpdateMonthlyCategoryStatisticsHandler;
use App\Statistics\Application\Command\UpdateMonthlyStatistics\UpdateMonthlyStatisticsCommand;
use App\Statistics\Application\Command\UpdateMonthlyStatistics\UpdateMonthlyStatisticsHandler;
use App\Statistics\Domain\repositories\MonthlyCategoryStatisticRepository;
use App\Statistics\Domain\repositories\MonthlyStatisticRepository;

class StatisticsEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private readonly MonthlyStatisticRepository $monthlyStatisticRepository,
        private readonly MonthlyCategoryStatisticRepository $monthlyCategoryStatisticRepository,
    )
    {
    }

    public function handle(DomainEvent $event): void
    {
        if (get_class($event) === OperationSaved::class) {
            $this->updateMonthlyStatistics($event);
            $this->updateMonthlyByCategoryStatistics($event);
        }
        if (get_class($event) === OperationDeleted::class) {
            $this->retrieveFromMonthlyStatistics($event);
            $this->retrieveFromMonthlyByCategoryStatistics($event);
        }
    }

    public function isSubscribeTo(DomainEvent $event): bool
    {
        return get_class($event) === OperationSaved::class ||
            get_class($event) === OperationDeleted::class;
    }

    private function updateMonthlyStatistics(OperationSaved $event): void
    {
        $command = new UpdateMonthlyStatisticsCommand(
          composedId: $event->monthlyStatsComposedId,
          userId: $event->userId,
          year: $event->year,
          month: $event->month,
          previousAmount: $event->previousAmount,
          newAmount: $event->newAmount,
          operationType: $event->type
        );
        (new UpdateMonthlyStatisticsHandler(
            repository: $this->monthlyStatisticRepository
        ))->handle($command);
    }

    private function updateMonthlyByCategoryStatistics(OperationSaved $event): void
    {
        $command = new UpdateMonthlyCategoryStatisticsCommand(
            composedId: $event->monthlyStatsByCategoryComposedId,
            userId: $event->userId,
            year: $event->year,
            month: $event->month,
            previousAmount: $event->previousAmount,
            newAmount: $event->newAmount,
            operationType: $event->type,
            categoryId: $event->categoryId,
        );
        (new UpdateMonthlyCategoryStatisticsHandler(
            repository: $this->monthlyCategoryStatisticRepository,
        ))->handle($command);
    }

    private function retrieveFromMonthlyStatistics(OperationDeleted $event): void
    {
        $command = new UpdateMonthlyStatisticsCommand(
            composedId: $event->monthlyStatisticsComposedId,
            userId: $event->userId,
            year: $event->year,
            month: $event->month,
            previousAmount: $event->previousAmount,
            newAmount: 0,
            operationType: $event->type,
            toDelete: $event->isDeleted,
        );
        (new UpdateMonthlyStatisticsHandler(
            repository: $this->monthlyStatisticRepository
        ))->handle($command);
    }

    private function retrieveFromMonthlyByCategoryStatistics(OperationDeleted $event): void
    {
        $command = new UpdateMonthlyCategoryStatisticsCommand(
            composedId: $event->monthlyStatisticsByCategoryComposedId,
            userId: $event->userId,
            year: $event->year,
            month: $event->month,
            previousAmount: $event->previousAmount,
            newAmount: 0,
            operationType: $event->type,
            categoryId: $event->categoryId,
            toDelete: $event->isDeleted,
        );
        (new UpdateMonthlyCategoryStatisticsHandler(
            repository: $this->monthlyCategoryStatisticRepository
        ))->handle($command);
    }

}
