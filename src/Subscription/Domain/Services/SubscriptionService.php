<?php

namespace App\Subscription\Domain\Services;


use App\Subscription\Domain\Exceptions\SubscriptionCannotPermitAccountCreationException;
use App\Subscription\Domain\Exceptions\SubscriptionCannotPermitOperationException;

interface SubscriptionService
{
    /**
     * @param string $userId
     * @return array
     */
    public function getUserSubscriptionData(string $userId): array;

    /**
     * @param string $subscription_id
     * @return array
     */
    public function getSubscriptionData(string $subscription_id): array;

    /**
     * @param string $userId
     * @param int $leftToken
     * @return void
     */
    public function updateUserToken(string $userId, int $leftToken): void;

    /**
     * @param string $userId
     * @param int $leftNbOperations
     * @return void
     */
    public function updateUserNbOperations(string $userId, int $leftNbOperations): void;

    /**
     * @param string $userId
     * @param int $consumedToken
     * @return void
     */
    public function retrieveUserToken(string $userId, int $consumedToken): void;

    /**
     * @param string $userId
     * @param int $count
     * @return void
     */
    public function updateNbAccounts(string $userId, int $count): void;

    /**
     * @param string $userId
     * @param int $count
     * @return void
     */
    public function retrieveOperation(string $userId, int $count): void;

    /**
     * @param string $userId
     * @return void
     * @throws SubscriptionCannotPermitAccountCreationException
     */
    public function checkIfCanCreateAccount(string $userId): void;

    /**
     * @param string $userId
     * @return void
     * @throws SubscriptionCannotPermitOperationException
     */
    public function checkIfCanMakeOperation(string $userId): void;
}
