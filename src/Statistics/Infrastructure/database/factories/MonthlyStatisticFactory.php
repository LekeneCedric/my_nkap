<?php

namespace App\Statistics\Infrastructure\database\factories;

use App\Statistics\Infrastructure\Model\MonthlyStatistic;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonthlyStatisticFactory extends Factory
{
    protected $model = MonthlyStatistic::class;

    public function definition(): array
    {
        return [
            'composed_id' => $this->faker->uuid,
            'user_id' => $this->faker->uuid,
            'month' => $this->faker->monthName,
            'year' => $this->faker->year,
            'total_income' => $this->faker->randomFloat(2, 0, 1000),
            'total_expense' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
