<?php

namespace App\Subscription\Infrastructure\Repositories;

use App\Subscription\Domain\Repository\SubscriptionRepository;
use App\Subscription\Domain\Subscription;
use App\Subscription\Infrastructure\Model\Subscription AS SubscriptionModel;

class EloquentSubscriptionRepository implements SubscriptionRepository
{

    public function ofId(string $subscriptionId): ?Subscription
    {
        return SubscriptionModel::whereUuid($subscriptionId)
            ->first()
            ->toDomain();
    }
}
