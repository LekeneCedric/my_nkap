<?php

namespace App\Subscription\Domain;

class SubscriberSubscription
{
    private function __construct(
        readonly string $id,
        readonly string $userId,
        private string $subscriptionId,
        private int $startDate,
        private int $endDate,
        private int $nbToken,
        private int $nbOperations,
        private int $nbAccounts,
    )
    {
    }

    public static function create(
        string $id,
        string $userId,
        string $subscriptionId,
        int $startDate,
        int $endDate,
        int $nbToken,
        int $nbOperations,
        int $nbAccounts,
    ): SubscriberSubscription
    {
        return new self(
            id: $id,
            userId: $userId,
            subscriptionId: $subscriptionId,
            startDate: $startDate,
            endDate: $endDate,
            nbToken: $nbToken,
            nbOperations: $nbOperations,
            nbAccounts: $nbAccounts,
        );
    }

    public function update(
        string $subscriptionId,
        int $startDate,
        int $endDate,
        int $nbToken,
        int $nbOperations,
        int $nbAccounts,
    ): void
    {
        $this->subscriptionId = $subscriptionId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->nbToken = $nbToken;
        $this->nbOperations = $nbOperations;
        $this->nbAccounts = $nbAccounts;
    }

    /**
     * @return string
     */
    public function subscriptionId(): string
    {
        return $this->subscriptionId;
    }

    /**
     * @return int
     */
    public function startDate(): int
    {
        return $this->startDate;
    }

    /**
     * @return int
     */
    public function endDate(): int
    {
        return $this->endDate;
    }

    /**
     * @return int
     */
    public function nbToken(): int
    {
        return $this->nbToken;
    }

    /**
     * @return int
     */
    public function nbOperations(): int
    {
        return $this->nbOperations;
    }

    /**
     * @return int
     */
    public function nbAccounts(): int
    {
        return $this->nbAccounts;
    }
}
