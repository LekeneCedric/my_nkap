<?php

namespace App\Statistics\Application\Command\UpdateMonthlyStatistics;

use App\Operation\Domain\OperationTypeEnum;
use App\User\Infrastructure\Models\User;

class UpdateMonthlyStatisticsCommand
{
    /**
     * @param string $composedId
     * @param string $userId
     * @param int $year
     * @param int $month
     * @param float $previousAmount
     * @param float $newAmount
     * @param OperationTypeEnum $operationType
     * @param bool $toDelete
     */
    public function __construct(
        public string $composedId,
        public string $userId,
        public int $year,
        public int $month,
        public float $previousAmount,
        public float $newAmount,
        public OperationTypeEnum $operationType,
        public bool $toDelete = false
    )
    {
    }
}
