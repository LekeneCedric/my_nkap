<?php

namespace App\Subscription\Infrastructure\database\factories;

use App\Subscription\Domain\Enums\SubscriptionPlansEnum;
use App\Subscription\Infrastructure\Model\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'name' => SubscriptionPlansEnum::FREE_PLAN->name,
            'price' => $this->faker->numberBetween(0, 0),
            'nb_token_per_day' => $this->faker->numberBetween(0, 0),
            'nb_operations_per_day' => $this->faker->numberBetween(0, 0),
            'nb_accounts' => $this->faker->numberBetween(0, 0),
        ];
    }
}
