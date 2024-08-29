<?php

namespace App\Statistics\Domain\Subscribers;

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
        if ($event instanceof OperationSaved::class) {
            $this->updateMonthlyStatistics($event);
            $this->updateMonthlyByCategoryStatistics($event);
        }
    }

    public function isSubscribeTo(DomainEvent $event): bool
    {
        return $event instanceof OperationSaved::class;
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
            composedId: $event->monthlyStatsBycategoryComposedId,
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

}
