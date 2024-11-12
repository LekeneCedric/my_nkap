<?php

namespace App\Subscription\Tests\Units;

use App\Shared\Domain\VO\Id;
use App\Subscription\Application\Command\Subscribe\SubscriptionCommand;
use App\Subscription\Application\Command\Subscribe\SubscriptionHandler;
use App\Subscription\Application\Command\Subscribe\SubscriptionResponse;
use App\Subscription\Domain\Enums\SubscriptionMessagesEnum;
use App\Subscription\Domain\Exceptions\NotFoundSubscriptionException;
use App\Subscription\Domain\Exceptions\SubscriberAlreadySubscribedToThisSubscriptionException;
use App\Subscription\Domain\Repository\SubscriberRepository;
use App\Subscription\Domain\Repository\SubscriptionRepository;
use App\Subscription\Domain\Subscriber;
use App\Subscription\Domain\SubscriberSubscription;
use App\Subscription\Domain\Subscription;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    private SubscriptionRepository $subscriptionRepository;
    private SubscriberRepository $subscriberRepository;
    public function setUp(): void
    {
        parent::setUp();
        $this->subscriptionRepository = new InMemorySubscriptionRepository();
        $this->subscriberRepository = new InMemorySubscriberRepository();
    }

    /**
     * @return void
     * @throws NotFoundSubscriptionException
     * @throws SubscriberAlreadySubscribedToThisSubscriptionException
     */
    public function test_user_can_subscribe()
    {
        $initSUT = $this->buildSUT();
        $command = new SubscriptionCommand(
            userId: $initSUT['userId'],
            subscriptionId: $initSUT['subscriptionIds'][1],
            nbMonth: 10,
        );

        $response = $this->subscribe($command);

        $this->assertTrue($response->isSubscribed);
        $this->assertEquals(SubscriptionMessagesEnum::SUBSCRIPTION_SUCCESS, $response->message);
        $this->assertEquals($response->subscriptionNbTokenPerDay, $initSUT['subscriptionNbTokenPerDay']);
        $this->assertEquals($response->subscriptionNbOperationsPerDay, $initSUT['subscriptionNbOperationsPerDay']);
        $this->assertEquals($response->subscriptionNbAccounts, $initSUT['subscriptionNbAccounts']);
        $this->assertEquals($this->subscriberRepository->subscribers[$initSUT['userId']]->subscription()->subscriptionId(), $initSUT['subscriptionIds'][1]);
    }

    /**
     * @return void
     * @throws NotFoundSubscriptionException
     * @throws SubscriberAlreadySubscribedToThisSubscriptionException
     */
    public function test_can_throw_exception_if_user_subscribe_to_same_subscription()
    {
        $initSUT = $this->buildSUT();
        $command = new SubscriptionCommand(
            userId: $initSUT['userId'],
            subscriptionId: $initSUT['subscriptionIds'][0],
            nbMonth: 10,
        );

        $this->expectException(SubscriberAlreadySubscribedToThisSubscriptionException::class);
        $this->subscribe($command);
    }

    /**
     * @return array
     */
    private function buildSUT(): array
    {
        $userId = (new Id())->value();
        $subscriptionId = (new Id())->value();
        $subscription2Id = (new Id())->value();
        $subscriptionNbTokenPerDay = 10;
        $subscriptionNbOperationsPerDay = 100;
        $subscriptionNbAccounts = 2;
        $subscription1 = new Subscription(
            subscriptionId: $subscriptionId,
            subscriptionNbTokenPerDay: $subscriptionNbTokenPerDay,
            subscriptionNbOperationsPerDay: $subscriptionNbOperationsPerDay,
            subscriptionNbAccounts: $subscriptionNbAccounts,
        );
        $subscription2 = new Subscription(
            subscriptionId: $subscription2Id,
            subscriptionNbTokenPerDay: $subscriptionNbTokenPerDay,
            subscriptionNbOperationsPerDay: $subscriptionNbOperationsPerDay,
            subscriptionNbAccounts: $subscriptionNbAccounts,
        );
        $subscriber = new Subscriber(
            userId: $userId,
            subscription: SubscriberSubscription::create(
                id: (new Id())->value(),
                userId: $userId,
                subscriptionId: $subscriptionId,
                startDate: time(),
                endDate: strtotime('+10 month'),
                nbToken: $subscriptionNbTokenPerDay,
                nbOperations: $subscriptionNbOperationsPerDay,
                nbAccounts: $subscriptionNbAccounts,
            ),
        );

        $this->subscriberRepository->subscribers[$userId] = $subscriber;
        $this->subscriptionRepository->subscriptions[$subscriptionId] = $subscription1;
        $this->subscriptionRepository->subscriptions[$subscription2Id] = $subscription2;

        return [
            'userId' => $userId,
            'subscriptionIds' => [$subscriptionId, $subscription2Id],
            'subscriptionNbTokenPerDay' => $subscriptionNbTokenPerDay,
            'subscriptionNbOperationsPerDay' => $subscriptionNbOperationsPerDay,
            'subscriptionNbAccounts' => $subscriptionNbAccounts,
        ];
    }

    /**
     * @param SubscriptionCommand $command
     * @return SubscriptionResponse
     * @throws NotFoundSubscriptionException
     * @throws SubscriberAlreadySubscribedToThisSubscriptionException
     */
    private function subscribe(SubscriptionCommand $command): SubscriptionResponse
    {
        $handler = new SubscriptionHandler(
            subscriptionRepository: $this->subscriptionRepository,
            subscriberRepository: $this->subscriberRepository,
        );
        return $handler->handle($command);
    }
}
