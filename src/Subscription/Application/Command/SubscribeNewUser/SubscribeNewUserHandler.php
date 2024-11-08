<?php

namespace App\Subscription\Application\Command\SubscribeNewUser;

use App\Shared\Domain\VO\Id;
use App\Subscription\Domain\Enums\SubscriptionPlansEnum;
use App\Subscription\Domain\Repository\SubscriberRepository;
use App\Subscription\Domain\Repository\SubscriptionRepository;
use App\Subscription\Domain\Subscriber;
use App\Subscription\Domain\SubscriberSubscription;
use App\Subscription\Domain\Subscription;

class SubscribeNewUserHandler
{
    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly SubscriberRepository   $subscriberRepository,
    )
    {
    }

    public function handle(SubscribeNewUserCommand $command): void
    {
        $basicPlanSubscription = $this->getBasicPlanSubscription();
        $subscriber = new Subscriber(
            userId: $command->userId,
            subscription: SubscriberSubscription::create(
                id: (new Id())->value(),
                userId: $command->userId,
                subscriptionId: $basicPlanSubscription->subscriptionId,
                startDate: time(),
                endDate: strtotime("+60 month"),
                nbToken: $basicPlanSubscription->subscriptionNbTokenPerDay,
                nbOperations: $basicPlanSubscription->subscriptionNbOperationsPerDay,
                nbAccounts: $basicPlanSubscription->subscriptionNbAccounts,
            ),
        );
        $this->subscriberRepository->save($subscriber);
    }

    private function getBasicPlanSubscription(): Subscription
    {
        return $this->subscriptionRepository->ofPlan(SubscriptionPlansEnum::FREE_PLAN);
    }
}
