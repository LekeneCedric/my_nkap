<?php

namespace App\Shared\Domain\Event;

interface DomainEventSubscriber
{
    public function handle(DomainEvent $event): void;
    public function isSubscribeTo(DomainEvent $event): bool;
}
