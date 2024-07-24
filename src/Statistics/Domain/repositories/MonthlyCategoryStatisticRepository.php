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

    public function create(MonthlyCategoryStatistic $monthlyCategoryStatistic): void;
    public function update(MonthlyCategoryStatistic $monthlyCategoryStatistic): void;

}
