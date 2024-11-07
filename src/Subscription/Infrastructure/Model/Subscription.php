<?php

namespace App\Subscription\Infrastructure\Model;

use App\Subscription\Infrastructure\database\factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Subscription\Domain\Subscription AS SubscriptionDomain;
/**
 * @method static whereUuid(string $subscriptionId)
 * @property mixed $uuid
 * @property mixed $nb_token_per_day
 * @property mixed $nb_operations_per_day
 */
class Subscription extends Model
{
    use HasFactory;

    protected static function newFactory(): SubscriptionFactory
    {
        return SubscriptionFactory::new();
    }

    public function toDomain(): SubscriptionDomain
    {
        return new SubscriptionDomain(
            subscriptionId: $this->uuid,
            subscriptionNbTokenPerDay: $this->nb_token_per_day,
            subscriptionNbOperationsPerDay: $this->nb_operations_per_day
        );
    }
}
