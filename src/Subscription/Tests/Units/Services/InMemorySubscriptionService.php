<?php

namespace App\Subscription\Tests\Units\Services;

use App\Subscription\Domain\Services\SubscriptionService;

class InMemorySubscriptionService implements SubscriptionService
{

    public function getUserSubscriptionData(string $userId): array
    {
        return [];
    }

    public function getSubscriptionData(string $subscription_id): array
    {
        return [];
    }

    public function updateUserToken(string $userId, int $leftToken): void
    {
        // TODO: Implement updateUserToken() method.
    }

    public function updateUserNbOperations(string $userId, int $leftNbOperations): void
    {
        // TODO: Implement updateUserNbOperations() method.
    }

    public function retrieveUserToken(string $userId, int $consumedToken): void
    {
        // TODO: Implement retrieveUserToken() method.
    }

    public function updateNbAccounts(string $userId, int $count): void
    {
        // TODO: Implement retrieveNbAccounts() method.
    }

    public function retrieveOperation(string $userId, int $count): void
    {
        // TODO: Implement retrieveOperation() method.
    }

    public function checkIfCanCreateAccount(string $userId): void
    {
        // TODO: Implement checkIfCanCreateAccount() method.
    }

    /**
     * @param string $userId
     * @return void
     */
    public function checkIfCanMakeOperation(string $userId): void
    {
        // TODO: Implement checkIfCanMakeOperation() method.
    }
}
