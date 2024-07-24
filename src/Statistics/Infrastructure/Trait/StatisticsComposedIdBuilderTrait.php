<?php

namespace App\Statistics\Infrastructure\Trait;

trait StatisticsComposedIdBuilderTrait
{
    public function buildMonthlyStatisticsComposedId(
        int $month,
        int $year,
        string $userId,
    ): string
    {
        return $userId.'#'.$year.'#'.$month;
    }

    public function buildMonthlyCategoryStatisticsComposedId(
        int $month,
        int $year,
        string $userId,
        string $categoryId,
    ): string
    {
        return $userId.'#'.$year.'#'.$month.'#'.$categoryId;
    }
}
