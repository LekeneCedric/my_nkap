<?php

namespace App\Subscription\Domain;

class Subscription
{
    public function __construct(
        readonly string $subscriptionId,
        readonly int    $subscriptionNbTokenPerDay,
        readonly int    $subscriptionNbOperationsPerDay
    )
    {
    }
}
