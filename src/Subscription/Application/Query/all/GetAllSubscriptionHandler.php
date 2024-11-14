<?php

namespace App\Subscription\Application\Query\all;

use App\Subscription\Domain\Repository\SubscriptionRepository;

class GetAllSubscriptionHandler
{
    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
    )
    {
    }

    public function handle(GetAllSubscriptionCommand $command): GetAllSubscriptionSubscription
    {
        $response = new GetAllSubscriptionSubscription();
        $response->subscriptions = $this->subscriptionRepository->all(userId: $command->userId);
        return $response;
    }
}
