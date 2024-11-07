<?php

namespace App\Subscription\Tests\Units;

use App\Subscription\Domain\Repository\SubscriptionRepository;
use App\Subscription\Domain\Subscription;

class InMemorySubscriptionRepository implements SubscriptionRepository
{
    /**
     * @var SubscriptionTest[]
     */
    public array $subscriptions;

    public function ofId(string $subscriptionId): ?Subscription
    {
        return $this->subscriptions[$subscriptionId] ?? null;
    }
}
