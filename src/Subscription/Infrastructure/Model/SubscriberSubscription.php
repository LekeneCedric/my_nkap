<?php

namespace App\Subscription\Infrastructure\Model;

use App\Subscription\Infrastructure\Factories\SubscriberSubscriptionFactory;
use App\User\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Subscription\Domain\SubscriberSubscription AS SubscriberSubscriptionDomain;
/**
 * @method static whereUserId($userId)
 * @method static create($data)
 * @property mixed $start_date
 * @property mixed $end_date
 * @property mixed $nb_token
 * @property mixed $nb_operations
 * @property mixed $uuid
 * @property mixed $user_id
 * @property mixed $subscription_id
 * @property mixed $nb_accounts
 */
class SubscriberSubscription extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected static function newFactory(): SubscriberSubscriptionFactory
    {
        return SubscriberSubscriptionFactory::new();
    }

    public function toDomain(): SubscriberSubscriptionDomain
    {
        $userId = User::select(['uuid'])->where('id', $this->user_id)->first()->uuid;
        $subscriptionId = Subscription::select(['uuid'])->where('id', $this->subscription_id)->first()->uuid;
        return SubscriberSubscriptionDomain::create(
            id: $this->uuid,
            userId: $userId,
            subscriptionId: $subscriptionId,
            startDate: $this->start_date,
            endDate: $this->end_date,
            nbToken: $this->nb_token,
            nbOperations: $this->nb_operations,
            nbAccounts: $this->nb_accounts,
        );
    }
}
