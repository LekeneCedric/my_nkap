<?php

namespace App\User\Domain;

use App\Shared\Domain\Event\DomainEvent;
use DateTimeImmutable;

class UserVerified implements DomainEvent
{
    private DateTimeImmutable $occuredOn;

    public function __construct(
        public readonly string $userId,
    )
    {
    }

    public function occuredOn(): DateTimeImmutable
    {
        return $this->occuredOn;
    }
}
