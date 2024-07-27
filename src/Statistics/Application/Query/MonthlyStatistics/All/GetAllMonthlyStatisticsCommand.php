<?php

namespace App\Statistics\Application\Query\MonthlyStatistics\All;

class GetAllMonthlyStatisticsCommand
{
    public function __construct(
        public string $userId,
        public int $year,
        public int $month,
    )
    {
    }
}
