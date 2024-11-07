<?php

namespace App\Subscription\Infrastructure\Repositories;

use App\Subscription\Domain\Repository\SubscriberRepository;
use App\Subscription\Domain\Subscriber;
use App\Subscription\Infrastructure\Model\SubscriberSubscription;
use App\Subscription\Infrastructure\Model\Subscription;
use App\User\Infrastructure\Models\User;

class EloquentSubscriberRepository implements SubscriberRepository
{
    /**
     * @param string $userId
     * @return Subscriber|null
     */
    public function ofUserId(string $userId): ?Subscriber
    {
        $user_id = User::select(['id'])->where('uuid', $userId)->first()->id;
        $subscriberSubscription = SubscriberSubscription::whereUserId($user_id)
            ->first()
            ->toDomain();
        return new Subscriber(
            userId: $userId,
            subscription: $subscriberSubscription,
        );
    }

    /**
     * @param Subscriber $subscriber
     * @return void
     */
    public function save(Subscriber $subscriber): void
    {
        $data = $subscriber->toArray();
        $userId = User::select(['id'])->where('uuid', $data['user_id'])->first()->id;
        $subscriptionId = Subscription::select(['id'])->where('uuid', $data['subscription_id'])->first()->id;
        SubscriberSubscription::create([
            ...$data,
            'user_id' => $userId,
            'subscription_id' => $subscriptionId,
        ]);
    }
}
