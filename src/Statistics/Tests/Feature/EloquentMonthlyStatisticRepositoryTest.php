<?php

namespace App\Statistics\Tests\Feature;

use App\Shared\Domain\VO\Id;
use App\Statistics\Domain\MonthlyStatistic;
use App\Statistics\Domain\repositories\MonthlyStatisticRepository;
use App\Statistics\Infrastructure\Repositories\EloquentMonthlyStatisticRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Statistics\Infrastructure\Model\MonthlyStatistic AS MonthlyStatisticModel;
class EloquentMonthlyStatisticRepositoryTest extends TestCase
{
    use RefreshDatabase;
    private MonthlyStatisticRepository $repository;
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentMonthlyStatisticRepository();
    }

    public function test_can_create_monthly_statistic()
    {
        $composedId = (new Id())->value();
        $monthlyStatistic = MonthlyStatistic::create(
            composedId: $composedId,
            userId: (new Id())->value(),
            year: 2028,
            month: 1
        );
        $this->repository->create($monthlyStatistic);
        $monthlyStatisticDb = $this->repository->ofComposedId($composedId);
        $this->assertNotEmpty($monthlyStatisticDb);
    }

    public function test_can_update_monthly_statistic()
    {
        $monthlyStatisticFake = MonthlyStatisticModel::factory()->create();
        $monthlyStatistic = MonthlyStatistic::createFromModel(
            id: $monthlyStatisticFake->id,
            composedId: $monthlyStatisticFake->composed_id,
            userId: $monthlyStatisticFake->user_id,
            year: $monthlyStatisticFake->year,
            month: $monthlyStatisticFake->month,
            totalIncome: 0,
            totalExpense: 50000,
        );
        $this->repository->update($monthlyStatistic);
        $monthlyStatisticDb = $this->repository->ofComposedId($monthlyStatisticFake->composed_id);
        $this->assertNotEmpty($monthlyStatisticDb);
        $this->assertEquals(50000, $monthlyStatisticDb->totalExpense);
    }
}
