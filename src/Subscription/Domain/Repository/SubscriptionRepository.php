<?php

namespace App\Subscription\Domain\Repository;

use App\Subscription\Application\Query\all\SubscriptionDto;
use App\Subscription\Domain\Enums\SubscriptionPlansEnum;
use App\Subscription\Domain\Subscription;

interface SubscriptionRepository
{
    /**
     * @param string $subscriptionId
     * @return Subscription|null
     */
    public function ofId(string $subscriptionId): ?Subscription;

    /**
     * @param SubscriptionPlansEnum $plan
     * @return Subscription|null
     */
    public function ofPlan(SubscriptionPlansEnum $plan): ?Subscription;

    /**
     * @param string $userId
     * @return SubscriptionDto[]
     */
    public function all(string $userId): array;
}
