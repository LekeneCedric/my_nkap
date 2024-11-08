<?php

namespace App\Subscription\Domain\Subscribers;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventSubscriber;
use App\Subscription\Application\Command\SubscribeNewUser\SubscribeNewUserCommand;
use App\Subscription\Application\Command\SubscribeNewUser\SubscribeNewUserHandler;
use App\Subscription\Domain\Repository\SubscriberRepository;
use App\Subscription\Domain\Repository\SubscriptionRepository;
use App\User\Domain\UserVerified;

class SubscriptionEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly SubscriberRepository   $subscriberRepository,
    )
    {
    }

    public function handle(DomainEvent $event): void
    {
        if (get_class($event) === UserVerified::class) {
            $command = new SubscribeNewUserCommand(
                userId: $event->userId,
            );
            (new SubscribeNewUserHandler(
                subscriptionRepository: $this->subscriptionRepository,
                subscriberRepository: $this->subscriberRepository
            ))->handle($command);
        }
    }

    public function isSubscribeTo(DomainEvent $event): bool
    {
        return get_class($event) === UserVerified::class;
    }
}
