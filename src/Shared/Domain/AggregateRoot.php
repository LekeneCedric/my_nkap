<?php

namespace App\Shared\Domain;

use App\Shared\Domain\Event\DomainEventPublisher;

abstract class AggregateRoot
{
    protected DomainEventPublisher $domainEventPublisher;

    public function __construct() {
        $this->domainEventPublisher = app(DomainEventPublisher::class);
    }

}
