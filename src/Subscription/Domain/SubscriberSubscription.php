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
        int $nbOperations
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
        );
    }

    public function update(
        string $subscriptionId,
        int $startDate,
        int $endDate,
        int $nbToken,
        int $nbOperations
    ): void
    {
        $this->subscriptionId = $subscriptionId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->nbToken = $nbToken;
        $this->nbOperations = $nbOperations;
    }

    /**
     * @return string
     */
    public function subscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function startDate(): int
    {
        return $this->startDate;
    }

    public function endDate(): int
    {
        return $this->endDate;
    }

    public function nbToken(): int
    {
        return $this->nbToken;
    }

    public function nbOperations(): int
    {
        return $this->nbOperations;
    }
}
