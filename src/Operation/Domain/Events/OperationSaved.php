<?php

namespace App\Operation\Domain\Events;

use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\Event\DomainEvent;
use DateTimeImmutable;

class OperationSaved implements DomainEvent
{
    private DateTimeImmutable $occuredOn;

    public function __construct(
        public readonly string $accountId,
        public readonly float $previousAmount,
        public readonly float $newAmount,
        public readonly string $operationDate,
        public readonly OperationTypeEnum $type,
        public readonly string $userId,
        public readonly int $year,
        public readonly int $month,
        public readonly string $categoryId,
        public readonly string $monthlyStatsComposedId,
        public readonly string $monthlyStatsBycategoryComposedId,
    )
    {
        $this->occuredOn = new DateTimeImmutable();
    }

    /**
     * @return DateTimeImmutable
     */
    public function occuredOn(): DateTimeImmutable
    {
        return $this->occuredOn;
    }
}
