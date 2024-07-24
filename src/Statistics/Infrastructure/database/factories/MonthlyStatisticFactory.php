<?php

namespace App\Statistics\Infrastructure\database\factories;

use App\Shared\Domain\Enums\MonthEnum;
use App\Statistics\Infrastructure\Model\MonthlyStatistic;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonthlyStatisticFactory extends Factory
{
    protected $model = MonthlyStatistic::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'composed_id' => $this->faker->uuid,
            'user_id' => $this->faker->uuid,
            'month' => $this->faker->randomElement(MonthEnum::values()),
            'year' => $this->faker->year,
            'total_income' => $this->faker->randomFloat(2, 0, 1000),
            'total_expense' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
