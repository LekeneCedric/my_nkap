<?php

namespace App\Statistics\Domain;

use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\VO\Id;
use App\Statistics\Domain\Dto\MonthlyStatisticDto;

class MonthlyStatistic
{
    public function __construct(
        public readonly string $id,
        public readonly string $composedId,
        public readonly string $userId,
        public readonly int $year,
        public readonly int $month,
        public float $totalIncome,
        public float $totalExpense
    )
    {
    }

    /**
     * @param string $composedId
     * @param string $userId
     * @param int $year
     * @param int $month
     * @return MonthlyStatistic
     */
    public static function create(
        string $composedId,
        string $userId,
        int    $year,
        int    $month,
    ): MonthlyStatistic
    {
        return new self(
            id: (new Id())->value(),
            composedId: $composedId,
            userId: $userId,
            year: $year,
            month: $month,
            totalIncome: 0.0,
            totalExpense: 0.0
        );
    }

    /**
     * @param string $id
     * @param string $composedId
     * @param string $userId
     * @param int $year
     * @param int $month
     * @param float $totalIncome
     * @param float $totalExpense
     * @return MonthlyStatistic
     */
    public static function createFromModel(
        string $id,
        string $composedId,
        string $userId,
        int $year,
        int $month,
        float $totalIncome,
        float $totalExpense
    ): MonthlyStatistic
    {
        return new self(
            id: $id,
            composedId: $composedId,
            userId: $userId,
            year: $year,
            month: $month,
            totalIncome: $totalIncome,
            totalExpense: $totalExpense
        );
    }

    /**
     * @param float $previousAmount
     * @param float $newAmount
     * @param OperationTypeEnum $operationType
     * @return void
     */
    public function updateAfterSaveOperation(
        float $previousAmount,
        float $newAmount,
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
            if($previousAmount > 0) {
              $this->totalExpense -= $previousAmount;
            }
            $this->totalExpense += $newAmount;
        }
    }

    /**
     * @param float $previousAmount
     * @param OperationTypeEnum $operationType
     * @return void
     */
    public function updateAfterDeleteOperation(float $previousAmount, OperationTypeEnum $operationType): void
    {
        if ($operationType === OperationTypeEnum::INCOME) {
            $this->totalIncome -= $previousAmount;
        }
        if ($operationType === OperationTypeEnum::EXPENSE) {
            $this->totalExpense -= $previousAmount;
        }
    }

    public function toDto(): MonthlyStatisticDto
    {
        return new MonthlyStatisticDto(
            id: $this->id,
            composedId: $this->composedId,
            userId: $this->userId,
            year: $this->year,
            month: $this->month,
            totalIncome: $this->totalIncome,
            totalExpense: $this->totalExpense,
        );
    }
}
