<?php

namespace App\Statistics\Domain\repositories;

use App\Statistics\Domain\MonthlyCategoryStatistic;

interface MonthlyCategoryStatisticRepository
{
    /**
     * @param string $composedId
     * @return MonthlyCategoryStatistic|null
     */
    public function ofComposedId(string $composedId): ?MonthlyCategoryStatistic;

    /**
     * @param MonthlyCategoryStatistic $monthlyCategoryStatistic
     * @return void
     */
    public function create(MonthlyCategoryStatistic $monthlyCategoryStatistic): void;

    /**
     * @param MonthlyCategoryStatistic $monthlyCategoryStatistic
     * @return void
     */
    public function update(MonthlyCategoryStatistic $monthlyCategoryStatistic): void;

    /**
     * @param string $userId
     * @param int $year
     * @param int $month
     * @return array
     */
    public function ofFilterParams(string $userId, int $year, int $month): array;

}
