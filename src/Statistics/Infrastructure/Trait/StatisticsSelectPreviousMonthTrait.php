<?php

namespace App\Statistics\Infrastructure\Trait;

trait StatisticsSelectPreviousMonthTrait
{
    private function selectPreviousMonth(int $month, array $selectedMonths, int $limit): array
    {
        $nextMonth = $month == 1 ? 12 : $month - 1;
        if (count($selectedMonths) == $limit) {
            return $selectedMonths;
        }
        return $this->selectPreviousMonth($nextMonth, [...$selectedMonths, $month], $limit);
    }
}
