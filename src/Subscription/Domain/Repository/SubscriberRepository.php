<?php

namespace App\Subscription\Domain\Repository;

use App\Subscription\Domain\Subscriber;

interface SubscriberRepository
{
    /**
     * @param string $userId
     * @return Subscriber|null
     */
    public function ofUserId(string $userId): ?Subscriber;

    /**
     * @param Subscriber $subscriber
     * @return void
     */
    public function save(Subscriber $subscriber): void;
}
