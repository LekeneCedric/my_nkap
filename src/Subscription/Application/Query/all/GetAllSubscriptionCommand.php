<?php

namespace App\Subscription\Application\Query\all;

class GetAllSubscriptionCommand
{
    public function __construct(
        public string $userId,
    )
    {
    }
}
