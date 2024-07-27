<?php

namespace App\Statistics\Tests\Units\Repositories;

use App\Statistics\Domain\MonthlyStatistic;
use App\Statistics\Domain\repositories\MonthlyStatisticRepository;

class InMemoryMonthlyMonthlyStatisticRepository implements MonthlyStatisticRepository
{
    /**
     * @var MonthlyStatistic[]
     */
    public array $monthlyStatistics = [];
    public function ofComposedId(string $composedId): ?MonthlyStatistic
    {
        foreach ($this->monthlyStatistics as $statistic) {
            if ($statistic->toDto()->composedId === $composedId)
                return $statistic;
        }
        return null;
    }

    /**
     * @param MonthlyStatistic $monthlyStatistics
     * @return void
     */
    public function create(MonthlyStatistic $monthlyStatistics): void
    {
        $this->monthlyStatistics[] = $monthlyStatistics;
    }

    /**
     * @param MonthlyStatistic $monthlyStatistics
     * @return void
     */
    public function update(MonthlyStatistic $monthlyStatistics): void
    {
        $this->monthlyStatistics[] = $monthlyStatistics;
    }

    public function ofFilterParams(string $userId, int $year, int $month): array
    {
        return [];
    }
}
