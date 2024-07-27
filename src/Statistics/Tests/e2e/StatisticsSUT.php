<?php

namespace App\Statistics\Tests\e2e;

use App\Statistics\Infrastructure\Model\MonthlyCategoryStatistic;
use App\Statistics\Infrastructure\Model\MonthlyStatistic;

class StatisticsSUT
{

    public static function asSUT(): StatisticsSUT
    {
        return new self();
    }

    public function withExistingMonthlyStatistic(
        int $month,
        string $userId,
        float $totalIncome = 150000,
        float $totalExpense = 50000
    ): static
    {
        MonthlyStatistic::factory()->create([
            'user_id' => $userId,
            'month' => $month,
            'year' => 2024,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense
        ]);
        return $this;
    }

    public function build(): static
    {
        return $this;
    }

    public function withExistingMonthlyCategoryStatistics(int $year, int $month, string $userId): static
    {
        MonthlyCategoryStatistic::factory()->create([
            'year' => $year,
            'month' => $month,
            'user_id' => $userId,
            'total_income'  => 25000,
            'total_expense' => 50000
        ]);
        MonthlyCategoryStatistic::factory()->create([
            'year' => $year,
            'month' => $month,
            'user_id' => $userId,
            'total_income'  => 50000,
            'total_expense' => 75000
        ]);
        MonthlyCategoryStatistic::factory()->create([
            'year' => $year,
            'month' => $month,
            'user_id' => $userId,
            'total_income'  => 75000,
            'total_expense' => 100000
        ]);
        return $this;
    }
}
