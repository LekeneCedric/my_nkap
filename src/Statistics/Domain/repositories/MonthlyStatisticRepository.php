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

}
