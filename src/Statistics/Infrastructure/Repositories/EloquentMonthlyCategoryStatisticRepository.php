<?php

namespace App\Statistics\Infrastructure\Repositories;

use App\category\Infrastructure\Models\Category;
use App\Statistics\Domain\MonthlyCategoryStatistic;
use App\Statistics\Domain\repositories\MonthlyCategoryStatisticRepository;
use App\Statistics\Infrastructure\Model\MonthlyCategoryStatistic AS MonthlyCategoryStatisticModel;
use App\Statistics\Infrastructure\Trait\StatisticsSelectPreviousMonthTrait;

class EloquentMonthlyCategoryStatisticRepository implements MonthlyCategoryStatisticRepository
{
    use StatisticsSelectPreviousMonthTrait;
    public function ofComposedId(string $composedId): ?MonthlyCategoryStatistic
    {
        return MonthlyCategoryStatisticModel::whereComposedId($composedId)->first()?->toDomain();
    }

    public function create(MonthlyCategoryStatistic $monthlyCategoryStatistic): void
    {
        $categoryAdditionalInformations = Category::select(['icon', 'color', 'name'])->whereUuid($monthlyCategoryStatistic->toDto()->categoryId)
            ->first();

        $data = array_merge(
            $monthlyCategoryStatistic->toDto()->toCreateArray(),
            [
                'category_icon' => $categoryAdditionalInformations->icon,
                'category_label' => $categoryAdditionalInformations->name,
                'category_color' => $categoryAdditionalInformations->color,
            ]
        );

        MonthlyCategoryStatisticModel::create($data);
    }

    public function update(MonthlyCategoryStatistic $monthlyCategoryStatistic): void
    {
        MonthlyCategoryStatisticModel::whereComposedId($monthlyCategoryStatistic->toDto()->composedId)
            ->update($monthlyCategoryStatistic->toDto()->toUpdateArray());
    }

    public function ofFilterParams(string $userId, int $year, int $month): array
    {
        $selectedMonths = $this->selectPreviousMonth($month, [], 3);
        return MonthlyCategoryStatisticModel::whereUserId($userId)
            ->where('year', $year)
            ->whereIn('month', $selectedMonths)
            ->get()->toArray();
    }
}
