<?php

namespace App\Statistics\Application\Command\UpdateMonthlyCategoryStatistics;

use App\Operation\Domain\OperationTypeEnum;

class UpdateMonthlyCategoryStatisticsCommand
{

    public function __construct(
        public string $composedId,
        public string $userId,
        public int $year,
        public int $month,
        public float $previousAmount,
        public float $newAmount,
        public OperationTypeEnum $operationType,
        public string $categoryId,
        public bool $toDelete = false,
    )
    {
    }
}
