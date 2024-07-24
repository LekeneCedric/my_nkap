<?php

namespace App\Statistics\Infrastructure\Repositories;

use App\Statistics\Domain\MonthlyStatistic;
use App\Statistics\Domain\repositories\MonthlyStatisticRepository;
use App\Statistics\Infrastructure\Model\MonthlyStatistic AS MonthlyStatisticModel;
class EloquentMonthlyStatisticRepository implements MonthlyStatisticRepository
{

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
}
