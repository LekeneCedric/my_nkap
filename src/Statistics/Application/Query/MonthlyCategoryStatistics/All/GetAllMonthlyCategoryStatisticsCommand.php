<?php

namespace App\Statistics\Application\Query\MonthlyCategoryStatistics\All;

class GetAllMonthlyCategoryStatisticsCommand
{
    public function __construct(
        public string $userId,
        public int $year,
        public int $month,
    )
    {
    }
}
