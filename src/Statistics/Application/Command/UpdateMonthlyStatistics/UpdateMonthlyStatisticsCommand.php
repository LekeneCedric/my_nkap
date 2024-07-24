<?php

namespace App\Statistics\Application\Command\UpdateMonthlyStatistics;

use App\Operation\Domain\OperationTypeEnum;
use App\User\Infrastructure\Models\User;

class UpdateMonthlyStatisticsCommand
{
    /**
     * @var true
     */
    public bool $toDelete;

    public function __construct(
        public string $composedId,
        public string $userId,
        public int $year,
        public int $month,
        public float $previousAmount,
        public float $newAmount,
        public OperationTypeEnum $operationType,
    )
    {
        $this->toDelete = false;
    }
}
