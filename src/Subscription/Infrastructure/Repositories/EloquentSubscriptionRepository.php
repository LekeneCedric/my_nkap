<?php

namespace App\Subscription\Infrastructure\Repositories;

use App\Subscription\Domain\Enums\SubscriptionPlansEnum;
use App\Subscription\Domain\Repository\SubscriptionRepository;
use App\Subscription\Domain\Subscription;
use App\Subscription\Infrastructure\Model\Subscription AS SubscriptionModel;

class EloquentSubscriptionRepository implements SubscriptionRepository
{
    /**
     * @param string $subscriptionId
     * @return Subscription|null
     */
    public function ofId(string $subscriptionId): ?Subscription
    {
        return SubscriptionModel::whereUuid($subscriptionId)
            ->first()
            ->toDomain();
    }

    /**
     * @param SubscriptionPlansEnum $plan
     * @return Subscription|null
     */
    public function ofPlan(SubscriptionPlansEnum $plan): ?Subscription
    {
        return SubscriptionModel::whereName($plan->name)
            ->first()
            ->toDomain();
    }
}
