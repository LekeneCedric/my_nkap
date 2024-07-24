<?php

namespace App\Statistics\Tests\Units;

use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\VO\DateVO;
use App\Statistics\Application\Command\UpdateMonthlyCategoryStatistics\UpdateMonthlyCategoryStatisticsCommand;
use App\Statistics\Application\Command\UpdateMonthlyCategoryStatistics\UpdateMonthlyCategoryStatisticsHandler;
use App\Statistics\Domain\repositories\MonthlyCategoryStatisticRepository;
use App\Statistics\Infrastructure\Trait\StatisticsComposedIdBuilderTrait;
use App\Statistics\Tests\Units\Repositories\InMemoryMonthlyCategoryCategoryStatisticRepository;
use Tests\TestCase;

class UpdateOperationMonthlyCategoryStatisticsTest extends TestCase
{
    use StatisticsComposedIdBuilderTrait;
    private MonthlyCategoryStatisticRepository $repository;
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryMonthlyCategoryCategoryStatisticRepository();
    }

    public function test_can_create_monthly_category_statistics_after_add_operation()
    {
        $initSUT = UpdateStatisticsSUT::asSUT()
            ->build();

        $operationDate = '2024-09-30 00:00:00';
        $operationAmount = 100000;
        list($year, $month) = [(new
        DateVO($operationDate))->year(), (new DateVO($operationDate))->month()];
        $composedId = $this->buildMonthlyCategoryStatisticsComposedId(
            month: $month,
            year: $year,
            userId: $initSUT->userId,
            categoryId: $initSUT->categoryId,
        );

        $command = new UpdateMonthlyCategoryStatisticsCommand(
            composedId: $composedId,
            userId: $initSUT->userId,
            year: $year,
            month: $month,
            previousAmount: 0,
            newAmount: $operationAmount,
            operationType: OperationTypeEnum::INCOME,
            categoryId: $initSUT->categoryId,
        );

        $this->updateMonthlyCategoryStatisticsAfterAddOperation($command);

        $monthlyCategoryStatistics = $this->repository->ofComposedId($composedId);
        $this->assertEquals($operationAmount, $monthlyCategoryStatistics->toDto()->totalIncome);
        $this->assertEquals(0, $monthlyCategoryStatistics->toDto()->totalExpense);
        $this->assertEquals($month, $monthlyCategoryStatistics->toDto()->month);
        $this->assertEquals($year, $monthlyCategoryStatistics->toDto()->year);
        $this->assertEquals($initSUT->categoryId, $monthlyCategoryStatistics->toDto()->categoryId);
    }

    public function test_can_update_monthly_category_statistics_after_update_operation()
    {
        $operationDate = '2024-09-30 00:00:00';
        list($year, $month) = [(new DateVO($operationDate))->year(), (new DateVO($operationDate))->month()];
        $initSUT = UpdateStatisticsSUT::asSUT()
            ->withExistingMonthlyCategoryStatistics(
                year: $year,
                month: $month,
                totalIncome: 100000,
                totalExpense: 100000
            )
            ->build();
        $this->saveInitDataInMemory($initSUT);

        $composedId = $this->buildMonthlyCategoryStatisticsComposedId(
            month: $month,
            year: $year,
            userId: $initSUT->userId,
            categoryId: $initSUT->categoryId,
        );
        $operationAmount = 40000;
        $previousAmount = 50000;
        $shouldBeTotalExpense = $initSUT->monthlyCategoryStatistic->toDto()
            ->totalExpense - $previousAmount + $operationAmount;

        $command = new UpdateMonthlyCategoryStatisticsCommand(
            composedId: $composedId,
            userId: $initSUT->userId,
            year: $year,
            month: $month,
            previousAmount: $previousAmount,
            newAmount: $operationAmount,
            operationType: OperationTypeEnum::EXPENSE,
            categoryId: $initSUT->categoryId,
        );

        $this->updateMonthlyCategoryStatisticsAfterAddOperation($command);

        $monthlyCategoryStatistics = $this->repository->ofComposedId($composedId);
        $this->assertEquals(100000, $monthlyCategoryStatistics->toDto()->totalIncome);
        $this->assertEquals($shouldBeTotalExpense, $monthlyCategoryStatistics->toDto()->totalExpense);
        $this->assertEquals($month, $monthlyCategoryStatistics->toDto()->month);
        $this->assertEquals($year, $monthlyCategoryStatistics->toDto()->year);
        $this->assertEquals($initSUT->categoryId, $monthlyCategoryStatistics->toDto()->categoryId);
    }

    public function test_can_update_monthly_category_statistics_after_delete_operation()
    {
        $operationDate = '2024-09-30 00:00:00';
        list($year, $month) = [(new DateVO($operationDate))->year(), (new DateVO($operationDate))->month()];
        $initSUT = UpdateStatisticsSUT::asSUT()
            ->withExistingMonthlyCategoryStatistics(
                year: $year,
                month: $month,
                totalIncome: 100000,
                totalExpense: 100000
            )
            ->build();
        $this->saveInitDataInMemory($initSUT);

        $composedId = $this->buildMonthlyCategoryStatisticsComposedId(
            month: $month,
            year: $year,
            userId: $initSUT->userId,
            categoryId: $initSUT->categoryId,
        );
        $previousAmount = 50000;
        $shouldBeTotalExpense = $initSUT->monthlyCategoryStatistic->toDto()
                ->totalExpense - $previousAmount;

        $command = new UpdateMonthlyCategoryStatisticsCommand(
            composedId: $composedId,
            userId: $initSUT->userId,
            year: $year,
            month: $month,
            previousAmount: $previousAmount,
            newAmount: 0,
            operationType: OperationTypeEnum::EXPENSE,
            categoryId: $initSUT->categoryId,
        );
        $command->toDelete = true;

        $this->updateMonthlyCategoryStatisticsAfterAddOperation($command);

        $monthlyCategoryStatistics = $this->repository->ofComposedId($composedId);
        $this->assertEquals(100000, $monthlyCategoryStatistics->toDto()->totalIncome);
        $this->assertEquals($shouldBeTotalExpense, $monthlyCategoryStatistics->toDto()->totalExpense);
        $this->assertEquals($month, $monthlyCategoryStatistics->toDto()->month);
        $this->assertEquals($year, $monthlyCategoryStatistics->toDto()->year);
        $this->assertEquals($initSUT->categoryId, $monthlyCategoryStatistics->toDto()->categoryId);
    }
    private function updateMonthlyCategoryStatisticsAfterAddOperation(UpdateMonthlyCategoryStatisticsCommand $command): void
    {
        $handler = new UpdateMonthlyCategoryStatisticsHandler(
            repository: $this->repository,
        );
        $handler->handle($command);
    }

    private function saveInitDataInMemory(UpdateStatisticsSUT $initSUT): void
    {
        $this->repository->monthlyCategoryStatistics[] = $initSUT->monthlyCategoryStatistic;
    }
}
