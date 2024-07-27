<?php

namespace App\Statistics\Infrastructure\Repositories;

use App\Statistics\Domain\MonthlyStatistic;
use App\Statistics\Domain\repositories\MonthlyStatisticRepository;
use App\Statistics\Infrastructure\Model\MonthlyStatistic as MonthlyStatisticModel;
use App\Statistics\Infrastructure\Trait\StatisticsSelectPreviousMonthTrait;

class EloquentMonthlyStatisticRepository implements MonthlyStatisticRepository
{
    use StatisticsSelectPreviousMonthTrait;
    public function ofComposedId(string $composedId): ?MonthlyStatistic
    {
        return MonthlyStatisticModel::whereComposedId($composedId)->first()?->toDomain();
    }

    public function update(MonthlyStatistic $monthlyStatistics): void
    {
        MonthlyStatisticModel::whereComposedId($monthlyStatistics->toDto()->composedId)
            ->update($monthlyStatistics->toDto()->toUpdateArray());
    }

    public function create(MonthlyStatistic $monthlyStatistics): void
    {
        MonthlyStatisticModel::create($monthlyStatistics->toDto()->toCreateArray());
    }

    public function ofFilterParams(string $userId, int $year, int $month): array
    {
        $previousMonths = $this->selectPreviousMonth($month, [], 3);
        return MonthlyStatisticModel::whereUserId($userId)
            ->where('year', $year)
            ->whereIn('month', $previousMonths)
            ->get()->toArray();
    }


}
