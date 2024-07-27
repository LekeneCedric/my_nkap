<?php

namespace App\Statistics\Domain\repositories;

use App\Statistics\Domain\MonthlyStatistic;

interface MonthlyStatisticRepository
{
    /**
     * @param string $composedId
     * @return MonthlyStatistic|null
     */
    public function ofComposedId(string $composedId): ?MonthlyStatistic;

    /**
     * @param MonthlyStatistic $monthlyStatistics
     * @return void
     */
    public function create(MonthlyStatistic $monthlyStatistics): void;

    /**
     * @param MonthlyStatistic $monthlyStatistics
     * @return void
     */
    public function update(MonthlyStatistic $monthlyStatistics): void;

    /**
     * @param string $userId
     * @param int $year
     * @param int $month
     * @return array
     */
    public function ofFilterParams(string $userId, int $year, int $month): array;

}
