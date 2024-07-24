<?php

namespace App\Statistics\Tests\Units;

use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\VO\DateVO;
use App\Statistics\Application\Command\UpdateMonthlyStatistics\UpdateMonthlyStatisticsCommand;
use App\Statistics\Application\Command\UpdateMonthlyStatistics\UpdateMonthlyStatisticsHandler;
use App\Statistics\Domain\repositories\MonthlyStatisticRepository;
use App\Statistics\Infrastructure\Trait\StatisticsComposedIdBuilderTrait;
use App\Statistics\Tests\Units\Repositories\InMemoryMonthlyMonthlyStatisticRepository;
use Exception;
use Tests\TestCase;

class UpdateOperationMonthlyStatisticsTest extends TestCase
{
    use StatisticsComposedIdBuilderTrait;
    private MonthlyStatisticRepository $repository;
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryMonthlyMonthlyStatisticRepository();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_can_create_monthly_statistics_after_add_operation()
    {
        $initSUT = UpdateStatisticsSUT::asSUT()
            ->build();

        $operationDate = '2024-09-30 00:00:00';
        $operationAmount = 100000;
        list($year, $month) = [(new DateVO($operationDate))->year(), (new DateVO($operationDate))->month()];
        $composedId = $this->buildMonthlyStatisticsComposedId(
            month: $month,
            year: $year,
            userId: $initSUT->userId
        );

        $command = new UpdateMonthlyStatisticsCommand(
            composedId: $composedId,
            userId: $initSUT->userId,
            year: $year,
            month: $month,
            previousAmount: 0,
            newAmount: $operationAmount,
            operationType: OperationTypeEnum::INCOME,
        );

        $this->updateMonthlyStatisticsAfterAddOperation($command);

        $monthlyStatistics = $this->repository->ofComposedId($composedId);
        $this->assertEquals($operationAmount, $monthlyStatistics->toDto()->totalIncome);
        $this->assertEquals(0, $monthlyStatistics->toDto()->totalExpense);
        $this->assertEquals($month, $monthlyStatistics->toDto()->month);
        $this->assertEquals($year, $monthlyStatistics->toDto()->year);
    }

    public function test_can_update_monthly_statistics_after_update_operation()
    {
        $operationDate = '2024-10-30 00:00:00';
        list($year, $month) = [(new DateVO($operationDate))->year(), (new DateVO($operationDate))->month()];

        $initSUT = UpdateStatisticsSUT::asSUT()
            ->withExistingMonthlyStatistics(
                year: $year,
                month: $month,
                totalIncome: 100000,
                totalExpense: 100000,
            )
            ->build();
        $this->saveInitDataInMemory($initSUT);

        $composedId = $initSUT->monthlyStatistic->toDto()->composedId;
        $operationAmount = 40000;
        $previousAmount = 50000;
        $shouldBeTotalExpense = $initSUT->monthlyStatistic->toDto()
                ->totalExpense - $previousAmount + $operationAmount;

        $command = new UpdateMonthlyStatisticsCommand(
            composedId: $composedId,
            userId: $initSUT->userId,
            year: $year,
            month: $month,
            previousAmount: $previousAmount,
            newAmount: $operationAmount,
            operationType: OperationTypeEnum::EXPENSE,
        );

        $this->updateMonthlyStatisticsAfterAddOperation($command);

        $monthlyStatistics = $this->repository->ofComposedId($composedId);
        $this->assertEquals(100000, $monthlyStatistics->toDto()->totalIncome);
        $this->assertEquals($shouldBeTotalExpense, $monthlyStatistics->toDto()->totalExpense);
        $this->assertEquals($month, $monthlyStatistics->toDto()->month);
        $this->assertEquals($year, $monthlyStatistics->toDto()->year);
    }

    public function test_can_update_monthly_statistics_after_delete_operation()
    {
        $operationDate = '2024-10-30 00:00:00';
        list($year, $month) = [(new DateVO($operationDate))->year(), (new DateVO($operationDate))->month()];

        $initSUT = UpdateStatisticsSUT::asSUT()
            ->withExistingMonthlyStatistics(
                year: $year,
                month: $month,
                totalIncome: 100000,
                totalExpense: 100000,
            )
            ->build();
        $this->saveInitDataInMemory($initSUT);

        $composedId = $initSUT->monthlyStatistic->toDto()->composedId;
        $previousAmount = 50000;
        $shouldBeTotalExpense = $initSUT->monthlyStatistic->toDto()
                ->totalExpense - $previousAmount;

        $command = new UpdateMonthlyStatisticsCommand(
            composedId: $composedId,
            userId: $initSUT->userId,
            year: $year,
            month: $month,
            previousAmount: $previousAmount,
            newAmount: 0,
            operationType: OperationTypeEnum::EXPENSE,
        );
        $command->toDelete = true;

        $this->updateMonthlyStatisticsAfterAddOperation($command);

        $monthlyStatistics = $this->repository->ofComposedId($composedId);
        $this->assertEquals(100000, $monthlyStatistics->toDto()->totalIncome);
        $this->assertEquals($shouldBeTotalExpense, $monthlyStatistics->toDto()->totalExpense);
        $this->assertEquals($month, $monthlyStatistics->toDto()->month);
        $this->assertEquals($year, $monthlyStatistics->toDto()->year);
    }

    private function updateMonthlyStatisticsAfterAddOperation(UpdateMonthlyStatisticsCommand $command): void
    {
        $handler = new UpdateMonthlyStatisticsHandler(
            repository: $this->repository
        );
        $handler->handle($command);
    }

    private function saveInitDataInMemory(UpdateStatisticsSUT $initSUT): void
    {
        $this->repository->monthlyStatistics[] = $initSUT->monthlyStatistic;
    }
}
