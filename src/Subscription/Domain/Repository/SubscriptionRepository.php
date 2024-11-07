<?php

namespace App\Subscription\Domain\Repository;

use App\Subscription\Domain\Subscription;

interface SubscriptionRepository
{
    /**
     * @param string $subscriptionId
     * @return Subscription|null
     */
    public function ofId(string $subscriptionId): ?Subscription;
}
