<?php

namespace App\Subscription\Application\Command\Subscribe;

use App\Subscription\Domain\Exceptions\NotFoundSubscriptionException;
use App\Subscription\Domain\Exceptions\SubscriberAlreadySubscribedToThisSubscriptionException;
use App\Subscription\Domain\Repository\SubscriberRepository;
use App\Subscription\Domain\Repository\SubscriptionRepository;
use App\Subscription\Domain\Subscription;
use App\Subscription\Domain\SubscriptionMessagesEnum;

class SubscriptionHandler
{
    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly SubscriberRepository $subscriberRepository,
    )
    {
    }

    /**
     * @param SubscriptionCommand $command
     * @return SubscriptionResponse
     * @throws NotFoundSubscriptionException
     * @throws SubscriberAlreadySubscribedToThisSubscriptionException
     */
    public function handle(SubscriptionCommand $command): SubscriptionResponse
    {
        $response = new SubscriptionResponse();

        $subscriber = $this->subscriberRepository->ofUserId($command->userId);
        $subscription = $this->getSubscriptionOrThrowNotFoundSubscription($command->subscriptionId);

        $subscriber->subscribe(
            subscription: $subscription,
            nbMonth: $command->nbMonth
        );

        $this->subscriberRepository->save($subscriber);

        $response->isSubscribed = true;
        $response->subscriptionNbTokenPerDay = $subscription->subscriptionNbTokenPerDay;
        $response->subscriptionNbOperationsPerDay = $subscription->subscriptionNbOperationsPerDay;
        $response->message = SubscriptionMessagesEnum::SUBSCRIPTION_SUCCESS;

        return $response;
    }

    /**
     * @param string $subscriptionId
     * @return Subscription
     * @throws NotFoundSubscriptionException
     */
    private function getSubscriptionOrThrowNotFoundSubscription(string $subscriptionId): Subscription
    {
        $subscription = $this->subscriptionRepository->ofId($subscriptionId);
        if (!$subscription) {
            throw new NotFoundSubscriptionException();
        }
        return $subscription;
    }
}
