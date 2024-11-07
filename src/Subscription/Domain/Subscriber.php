<?php

namespace App\Subscription\Domain;

use App\Subscription\Domain\Exceptions\SubscriberAlreadySubscribedToThisSubscriptionException;

class Subscriber
{
    public function __construct(
        readonly string                         $userId,
        readonly SubscriberSubscription $subscription,
    )
    {
    }

    /**
     * @param Subscription $subscription
     * @param int $nbMonth
     * @return void
     * @throws SubscriberAlreadySubscribedToThisSubscriptionException
     */
    public function subscribe(Subscription $subscription, int $nbMonth): void
    {
        $this->checkIfUserAlreadySubscribedToThisSubscription($subscription);
        $this->subscription->update(
            subscriptionId: $subscription->subscriptionId,
            startDate: time(),
            endDate: strtotime("+$nbMonth month"),
            nbToken: $subscription->subscriptionNbTokenPerDay,
            nbOperations: $subscription->subscriptionNbOperationsPerDay,
        );
    }

    /**
     * @param Subscription $subscription
     * @return void
     * @throws SubscriberAlreadySubscribedToThisSubscriptionException
     */
    private function checkIfUserAlreadySubscribedToThisSubscription(Subscription $subscription): void
    {
        if ($this->subscription->subscriptionId() === $subscription->subscriptionId) {
            throw new SubscriberAlreadySubscribedToThisSubscriptionException();
        }
    }

    public function subscription(): SubscriberSubscription
    {
        return $this->subscription;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->subscription->id,
            'user_id' => $this->userId,
            'subscription_id' => $this->subscription->subscriptionId(),
            'start_date' => $this->subscription->startDate(),
            'end_date' => $this->subscription->endDate(),
            'nb_token' => $this->subscription->nbToken(),
            'nb_operations' => $this->subscription->nbOperations(),
        ];
    }
}
