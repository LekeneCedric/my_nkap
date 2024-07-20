<?php

namespace App\FinancialGoal\Domain\Dto;

class FinancialGoalDto
{
    public function __construct(
        public string $id,
        public string $accountId,
        public float $currentAmount,
        public bool $isComplete
    )
    {
    }

    public function toUpdateArray(): array
    {
        return [
          'current_amount' => $this->currentAmount,
          'is_complete' => $this->isComplete,
        ];
    }
}
