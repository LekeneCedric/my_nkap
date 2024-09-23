<?php

namespace App\Statistics\Tests\Units\Repositories;

use App\Statistics\Domain\MonthlyCategoryStatistic;
use App\Statistics\Domain\repositories\MonthlyCategoryStatisticRepository;

class InMemoryMonthlyCategoryCategoryStatisticRepository implements MonthlyCategoryStatisticRepository
{
    /**
     * @var MonthlyCategoryStatistic[]
     */
    public array $monthlyCategoryStatistics = [];
    public function ofComposedId(string $composedId): ?MonthlyCategoryStatistic
    {
        foreach ($this->monthlyCategoryStatistics as $monthlyCategoryStatistic)
        {
            if ($monthlyCategoryStatistic->toDto()->composedId === $composedId)
                return $monthlyCategoryStatistic;
        }
        return null;
    }

    public function create(MonthlyCategoryStatistic $monthlyCategoryStatistic): void
    {
        $this->monthlyCategoryStatistics[] = $monthlyCategoryStatistic;
    }

    public function update(MonthlyCategoryStatistic $monthlyCategoryStatistic): void
    {
        $this->monthlyCategoryStatistics[] = $monthlyCategoryStatistic;
    }

    public function ofFilterParams(string $userId, int $year, int $month): array
    {
        return [];
    }
}
