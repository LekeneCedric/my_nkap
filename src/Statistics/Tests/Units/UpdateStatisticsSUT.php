<?php

namespace App\Statistics\Tests\Units;

use App\Shared\Domain\VO\Id;
use App\Statistics\Domain\MonthlyCategoryStatistic;
use App\Statistics\Domain\MonthlyStatistic;
use App\Statistics\Infrastructure\Trait\StatisticsComposedIdBuilderTrait;

class UpdateStatisticsSUT
{
    use StatisticsComposedIdBuilderTrait;
    public string $userId;
    public string $categoryId;
    public ?MonthlyStatistic $monthlyStatistic;
    public ?MonthlyCategoryStatistic $monthlyCategoryStatistic;
    public static function asSUT(): UpdateStatisticsSUT
    {
        $self = new self();
        $self->userId = (new Id())->value();
        $self->categoryId = (new Id())->value();
        $self->monthlyStatistic = null;
        $self->monthlyCategoryStatistic = null;
        return $self;
    }

    public function build(): static
    {
        return $this;
    }

    public function withExistingMonthlyStatistics(
        int $year,
        int $month,
        float $totalIncome,
        float $totalExpense
    ): static
    {

        $composedId = $this->buildMonthlyStatisticsComposedId(
            month: $month,
            year: $year,
            userId: $this->userId,
        );
        $this->monthlyStatistic = MonthlyStatistic::createFromModel(
            id: (new Id())->value(),
            composedId: $composedId,
            userId: $this->userId,
            year: $year,
            month: $month,
            totalIncome: $totalIncome,
            totalExpense: $totalExpense
        );
        return $this;
    }

    public function withExistingMonthlyCategoryStatistics(
        int $year,
        int $month,
        float $totalIncome,
        float $totalExpense
    )
    {
        $composedId = $this->buildMonthlyCategoryStatisticsComposedId(
            month: $month,
            year: $year,
            userId: $this->userId,
            categoryId: $this->categoryId,
        );
        $this->monthlyCategoryStatistic = MonthlyCategoryStatistic::createFromModel(
            id: (new Id())->value(),
            composedId: $composedId,
            userId: $this->userId,
            year: $year,
            month: $month,
            totalIncome: $totalIncome,
            totalExpense: $totalExpense,
            categoryId: $this->categoryId,
        );
        return $this;
    }
}
