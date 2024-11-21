<?php

namespace App\Subscription\Application\Query\all;

class SubscriptionDto
{
    public string $id;
    public string $description;
    public string $name;
    public float $price;
    public int $nbTokenPerDay;
    public int $nbOperationsPerDay;
    public int $nbAccounts;
    public bool $isSubscribed;
}
