<?php

namespace App\Shared\Domain\Event;

class DomainEventPublisher
{
    private static ?DomainEventPublisher $instance = null;
    /**
     * @var DomainEventSubscriber[]
     */
    private array $subscribers = [];

    public static function instance(): DomainEventPublisher
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function subscribe(DomainEventSubscriber $subscriber): void
    {
        $this->subscribers[] = $subscriber;
    }

    public function publish(DomainEvent $domainEvent): void {
        foreach($this->subscribers as $subscriber) {
            if ($subscriber->isSubscribeTo($domainEvent)) {
                $subscriber->handle($domainEvent);
            }
        }
    }
}
