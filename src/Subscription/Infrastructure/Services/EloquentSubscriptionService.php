<?php

namespace App\Subscription\Infrastructure\Services;

use App\Subscription\Domain\Exceptions\SubscriptionCannotPermitAccountCreationException;
use App\Subscription\Domain\Exceptions\SubscriptionCannotPermitOperationException;
use App\Subscription\Domain\Services\SubscriptionService;
use App\Subscription\Infrastructure\Model\SubscriberSubscription;
use App\Subscription\Infrastructure\Model\Subscription;
use App\User\Infrastructure\Models\User;

class EloquentSubscriptionService implements SubscriptionService
{
    /**
     * @param string $userId
     * @return array
     */
    public function getUserSubscriptionData(string $userId): array
    {
        $user_id = User::select(['id'])->where('uuid', $userId)->first()->id;
        $subscriberSubscription = SubscriberSubscription::whereUserId($user_id)->first();
        $subscriptionId = Subscription::select(['uuid'])->whereId($subscriberSubscription->subscription_id)->first()->uuid;
        return [
            'subscriptionId' => $subscriptionId,
            'start_date' => $subscriberSubscription->start_date,
            'end_date' => $subscriberSubscription->end_date,
            'nb_token' => $subscriberSubscription->nb_token,
            'nb_operations' => $subscriberSubscription->nb_operations,
            'nb_accounts' => $subscriberSubscription->nb_accounts,
            'nb_token_updated_at' => $subscriberSubscription->nb_token_updated_at,
            'nb_operations_updated_at' => $subscriberSubscription->nb_operations_updated_at,
        ];
    }

    /**
     * @param string $subscriptionId
     * @return array
     */
    public function getSubscriptionData(string $subscriptionId): array
    {
        $subscription = Subscription::select(['nb_token_per_day', 'nb_operations_per_day', 'nb_accounts'])
            ->whereUuid($subscriptionId)->first();
        return [
            'nb_token_per_day' => $subscription->nb_token_per_day,
            'nb_operations_per_day' => $subscription->nb_operations_per_day,
            'nb_accounts' => $subscription->nb_accounts,
        ];
    }

    /**
     * @param string $userId
     * @param int $leftToken
     * @return void
     */
    public function updateUserToken(string $userId, int $leftToken): void
    {
        $user_id = User::select(['id'])->where('uuid', $userId)->first()->id;
        SubscriberSubscription::whereUserId($user_id)->update([
            'nb_token' => $leftToken,
            'nb_token_updated_at' => strtotime('now'),
        ]);
    }

    /**
     * @param string $userId
     * @param int $leftNbOperations
     * @return void
     */
    public function updateUserNbOperations(string $userId, int $leftNbOperations): void
    {
        $user_id = User::select(['id'])->where('uuid', $userId)->first()->id;
        SubscriberSubscription::whereUserId($user_id)->update([
            'nb_operations' => $leftNbOperations,
            'nb_operations_updated_at' => strtotime('now'),
        ]);
    }

    /**
     * @param string $userId
     * @param int $consumedToken
     * @return void
     */
    public function retrieveUserToken(string $userId, int $consumedToken): void
    {
        $user_id = User::select(['id'])->where('uuid', $userId)->first()->id;
        $subscriberSubscription = SubscriberSubscription::select(['nb_token'])->whereUserId($user_id)->first();
        $leftToken = $subscriberSubscription->nb_token - $consumedToken;
        if ($leftToken < 0) {
            $leftToken = 0;
        }
        $this->updateUserToken($userId, $leftToken);
    }

    /**
     * @param string $userId
     * @param int $count
     * @return void
     */
    public function retrieveOperation(string $userId, int $count): void
    {
        $user_id = User::select(['id'])->where('uuid', $userId)->first()->id;
        $currentNbOperations = SubscriberSubscription::select(['nb_operations'])
            ->whereUserId($user_id)->first()->nb_operations;
        SubscriberSubscription::whereUserId($user_id)->update([
            'nb_operations' => max($currentNbOperations - $count, 0),
            'nb_operations_updated_at' => strtotime('now'),
        ]);
    }

    /**
     * @param string $userId
     * @param int $count
     * @return void
     */
    public function updateNbAccounts(string $userId, int $count): void
    {
        $user_id = User::select(['id'])->where('uuid', $userId)->first()->id;
        $currentNbAccounts = SubscriberSubscription::select(['nb_accounts'])
            ->whereUserId($user_id)->first()->nb_accounts;
        SubscriberSubscription::whereUserId($user_id)->update([
            'nb_accounts' => $currentNbAccounts + $count,
        ]);
    }

    /**
     * @param string $userId
     * @return void
     * @throws SubscriptionCannotPermitAccountCreationException
     */
    public function checkIfCanCreateAccount(string $userId): void
    {
        $user_id = User::select(['id'])->where('uuid', $userId)->first()->id;
        $currentNbAccountsCanCreate = SubscriberSubscription::select(['nb_accounts'])
            ->whereUserId($user_id)->first()->nb_accounts;
        if ($currentNbAccountsCanCreate == 0) {
            throw new SubscriptionCannotPermitAccountCreationException();
        }
    }

    /**
     * @param string $userId
     * @return void
     * @throws SubscriptionCannotPermitOperationException
     */
    public function checkIfCanMakeOperation(string $userId): void
    {
        $user_id = User::select(['id'])->where('uuid', $userId)->first()->id;
        $currentNbOperations = SubscriberSubscription::select(['nb_operations'])
            ->whereUserId($user_id)->first()->nb_operations;
        if ($currentNbOperations == 0) {
            throw new SubscriptionCannotPermitOperationException();
        }
    }
}
