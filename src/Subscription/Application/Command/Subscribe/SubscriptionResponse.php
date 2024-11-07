<?php

namespace App\Subscription\Application\Command\Subscribe;

class SubscriptionResponse
{
    public bool $isSubscribed = false;
    public string $message = '';
    public int $subscriptionNbTokenPerDay = 0;
    public int $subscriptionNbOperationsPerDay = 0;
}
