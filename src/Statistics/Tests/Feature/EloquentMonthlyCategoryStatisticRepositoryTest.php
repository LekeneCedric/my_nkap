<?php

namespace App\Statistics\Tests\Feature;

use App\category\Infrastructure\Models\Category;
use App\Shared\Domain\VO\Id;
use App\Statistics\Domain\MonthlyCategoryStatistic;
use App\Statistics\Domain\repositories\MonthlyCategoryStatisticRepository;
use App\Statistics\Infrastructure\Model\MonthlyCategoryStatistic AS MonthlyCategoryStatisticModel;
use App\Statistics\Infrastructure\Repositories\EloquentMonthlyCategoryStatisticRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentMonthlyCategoryStatisticRepositoryTest extends TestCase
{
    use RefreshDatabase;
    private MonthlyCategoryStatisticRepository $repository;
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentMonthlyCategoryStatisticRepository();
    }

    public function test_can_create_monthly_category_statistic()
    {
        $composedId = (new Id())->value();
        $categoryId = Category::factory()->create()->uuid;
        $monthlyCategoryStatistic = MonthlyCategoryStatistic::create(
            composedId: $composedId,
            userId: (new Id())->value(),
            year: 2028,
            month: 1,
            categoryId: $categoryId,
        );
        $this->repository->create($monthlyCategoryStatistic);
        $this->assertNotEmpty(MonthlyCategoryStatisticModel::whereComposedId($monthlyCategoryStatistic->composedId));
    }

    public function test_can_update_monthly_category_statistic()
    {
        $monthlyCategoryStatisticFake = MonthlyCategoryStatisticModel::factory()->create();
        $monthlyCategoryStatistic = MonthlyCategoryStatistic::createFromModel(
            id: $monthlyCategoryStatisticFake->id,
            composedId: $monthlyCategoryStatisticFake->composed_id,
            userId: $monthlyCategoryStatisticFake->user_id,
            year: $monthlyCategoryStatisticFake->year,
            month: $monthlyCategoryStatisticFake->month,
            totalIncome: 300000,
            totalExpense: $monthlyCategoryStatisticFake->total_expense,
            categoryId: $monthlyCategoryStatisticFake->category_id,
        );
        $this->repository->update($monthlyCategoryStatistic);
        $monthlyCategoryStatisticDb = MonthlyCategoryStatisticModel::whereComposedId($monthlyCategoryStatisticFake->composed_id)->first();
        $this->assertNotEmpty($monthlyCategoryStatisticDb);
        $this->assertEquals(300000, $monthlyCategoryStatisticDb->total_income);
    }
}
