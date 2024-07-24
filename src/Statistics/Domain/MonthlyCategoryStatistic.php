<?php

namespace App\Statistics\Domain;

use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\VO\Id;
use App\Statistics\Domain\Dto\MonthlyCategoryStatisticDto;

class MonthlyCategoryStatistic
{
    public function __construct(
        public readonly string $id,
        public readonly string $composedId,
        public readonly string $userId,
        public readonly int    $year,
        public readonly int    $month,
        public readonly string $categoryId,
        public float           $totalIncome,
        public float           $totalExpense,
    )
    {
    }

    public function updateAfterSaveOperation(
        float             $previousAmount,
        float             $newAmount,
        OperationTypeEnum $operationType
    ): void
    {
        if ($operationType === OperationTypeEnum::INCOME) {
            if ($previousAmount > 0) {
                $this->totalIncome -= $previousAmount;
            }
            $this->totalIncome += $newAmount;
        }
        if ($operationType === OperationTypeEnum::EXPENSE) {
            if ($previousAmount > 0) {
                $this->totalExpense -= $previousAmount;
            }
            $this->totalExpense += $newAmount;
        }
    }

    public function updateAfterDeleteOperation(
        float             $previousAmount,
        OperationTypeEnum $operationType
    ): void
    {
        if ($operationType === OperationTypeEnum::INCOME) {
            $this->totalIncome -= $previousAmount;
        }
        if ($operationType === OperationTypeEnum::EXPENSE) {
            $this->totalExpense -= $previousAmount;
        }
    }

    public static function create(
        string $composedId,
        string $userId,
        int    $year,
        int    $month,
        string $categoryId
    ): MonthlyCategoryStatistic
    {
        return new self(
            id: (new Id())->value(),
            composedId: $composedId,
            userId: $userId,
            year: $year,
            month: $month,
            categoryId: $categoryId,
            totalIncome: 0,
            totalExpense: 0,
        );
    }

    public static function createFromModel(
        string $id,
        string $composedId,
        string $userId,
        int $year,
        int $month,
        float $totalIncome,
        float $totalExpense,
        string $categoryId
    ): MonthlyCategoryStatistic
    {
        return new self(
            id: $id,
            composedId: $composedId,
            userId: $userId,
            year: $year,
            month: $month,
            categoryId: $categoryId,
            totalIncome: $totalIncome,
            totalExpense: $totalExpense,
        );
    }

    public function toDto(): MonthlyCategoryStatisticDto
    {
        return new MonthlyCategoryStatisticDto(
            id: $this->id,
            composedId: $this->composedId,
            userId: $this->userId,
            year: $this->year,
            month: $this->month,
            categoryId: $this->categoryId,
            totalIncome: $this->totalIncome,
            totalExpense: $this->totalExpense,
        );
    }
}
