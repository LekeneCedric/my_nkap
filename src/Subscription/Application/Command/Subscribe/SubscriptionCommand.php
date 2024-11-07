<?php

namespace App\Subscription\Application\Command\Subscribe;

class SubscriptionCommand
{
    public function __construct(
        public string $userId,
        public string $subscriptionId,
        public int $nbMonth,
    )
    {
    }
}
