<?php

namespace App\Account\Infrastructure\database\factories;

use App\Account\Infrastructure\Model\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->name,
            'type' => $this->faker->title,
            'icon' => $this->faker->image,
            'color' => $this->faker->colorName,
            'balance' => $this->faker->numberBetween(0, 0),
            'total_incomes' => 0,
            'total_expenses' => 0,
            'is_include_in_total_balance' => $this->faker->boolean,
        ];
    }
}
