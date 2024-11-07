<?php

namespace App\Subscription\Infrastructure\Factories;

use App\Subscription\Infrastructure\Model\SubscriberSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriberSubscriptionFactory extends Factory
{
    protected $model = SubscriberSubscription::class;
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'user_id' => 1,
            'subscription_id' => 1,
            'start_date' => time(),
            'end_date' => strtotime("+12 month"),
            'nb_token' => 10,
            'nb_operations' => 10,
        ];
    }
}
