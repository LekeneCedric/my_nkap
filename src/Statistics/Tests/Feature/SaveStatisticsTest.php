<?php

namespace App\Statistics\Tests\Feature;

use App\Statistics\Infrastructure\Model\MonthlyCategoryStatistic;
use App\Statistics\Infrastructure\Model\MonthlyStatistic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SaveStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        DB::rollBack();
    }

    public function test_can_create_monthly_statistics()
    {
        MonthlyStatistic::factory(10)->create();
        $this->assertCount(10, MonthlyStatistic::all());
    }

    public function test_can_create_monthly_category_statistics()
    {
        MonthlyCategoryStatistic::factory(10)->create();
        $this->assertCount(10, MonthlyCategoryStatistic::all());
    }
}
