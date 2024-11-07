<?php

namespace App\Subscription\Tests\Units;

use App\Subscription\Domain\Repository\SubscriberRepository;
use App\Subscription\Domain\Subscriber;

class InMemorySubscriberRepository implements SubscriberRepository
{
    /**
     * @var Subscriber[]
     */
    public array $subscribers = [];
    public function ofUserId(string $userId): ?Subscriber
    {
        foreach ($this->subscribers as $subscriber) {
            if ($subscriber->userId === $userId) {
                return $subscriber;
            }
        }
        return null;
    }

    public function save(Subscriber $subscriber): void
    {
        $this->subscribers[$subscriber->userId] = $subscriber;
    }
}
