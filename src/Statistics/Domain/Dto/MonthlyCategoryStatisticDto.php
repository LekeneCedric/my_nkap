<?php

namespace App\Statistics\Domain\Dto;

class MonthlyCategoryStatisticDto
{
    public function __construct(
        public string $id,
        public string $composedId,
        public string $userId,
        public int $year,
        public int $month,
        public string $categoryId,
        public float $totalIncome,
        public float $totalExpense,
    )
    {
    }

    public function toCreateArray(): array
    {
        return [
          'id' => $this->id,
          'composed_id' => $this->composedId,
          'user_id' => $this->userId,
          'year' => $this->year,
          'month' => $this->month,
          'category_id' => $this->categoryId,
          'total_income' => $this->totalIncome,
          'total_expense' => $this->totalExpense,
        ];
    }

    public function toUpdateArray(): array
    {
        return [
          'total_income' => $this->totalIncome,
          'total_expense' => $this->totalExpense,
        ];
    }

}
