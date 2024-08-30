<?php

namespace App\Operation\Domain\Events;

use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\Event\DomainEvent;
use DateTimeImmutable;

class OperationDeleted implements DomainEvent
{
    private DateTimeImmutable $occuredOn;

    public function __construct(
        public readonly string $accountId,
        public readonly float $previousAmount,
        public readonly float $newAmount,
        public readonly string $date,
        public OperationTypeEnum $type,
        public bool $isDeleted,
        public string $monthlyStatisticsComposedId,
        public string $monthlyStatisticsByCategoryComposedId,
        public string $userId,
        public int $year,
        public int $month,
        public string $categoryId,

    )
    {
        $this->occuredOn = new DateTimeImmutable();
    }

    public function occuredOn(): DateTimeImmutable
    {
        return $this->occuredOn;
    }
}
