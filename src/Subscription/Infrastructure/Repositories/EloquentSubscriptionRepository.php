<?php

namespace App\Subscription\Infrastructure\Repositories;

use App\Subscription\Application\Query\all\SubscriptionDto;
use App\Subscription\Domain\Enums\SubscriptionPlansEnum;
use App\Subscription\Domain\Repository\SubscriptionRepository;
use App\Subscription\Domain\Subscription;
use App\Subscription\Infrastructure\Model\SubscriberSubscription;
use App\Subscription\Infrastructure\Model\Subscription AS SubscriptionModel;
use App\User\Infrastructure\Models\User;

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

    /**
     * @param string $userId
     * @return array|SubscriptionDto[]
     */
    public function all(string $userId): array
    {
        $user_id = User::select(['id'])->where('uuid', $userId)->first()->id;
        $currentUserSubscription_id = SubscriberSubscription::select(['subscription_id'])
            ->whereUserId($user_id)->first()->subscription_id;
        return SubscriptionModel::all()
            ->map(function(SubscriptionModel $subscription) use ($currentUserSubscription_id){
                $subscriptionDto = new SubscriptionDto();
                $subscriptionDto->id = $subscription->uuid;
                $subscriptionDto->name = $subscription->name;
                $subscriptionDto->description = $subscription->description;
                $subscriptionDto->price = $subscription->price;
                $subscriptionDto->nbTokenPerDay = $subscription->nb_token_per_day;
                $subscriptionDto->nbOperationsPerDay = $subscription->nb_operations_per_day;
                $subscriptionDto->nbAccounts = $subscription->nb_accounts;
                $subscriptionDto->isSubscribed = ($subscription->id == $currentUserSubscription_id);
                return $subscriptionDto;
            })
            ->toArray();
    }
}
