<?php

namespace App\Statistics\Infrastructure\database\factories;

use App\Statistics\Infrastructure\Model\MonthlyCategoryStatistic;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonthlyCategoryStatisticFactory extends Factory
{
    protected $model = MonthlyCategoryStatistic::class;

    public function definition(): array
    {
        return [
            'composed_id' => $this->faker->uuid,
            'user_id' => $this->faker->uuid,
            'month' => $this->faker->monthName,
            'year' => $this->faker->year,
            'category_id' => $this->faker->uuid,
            'category_icon' => $this->faker->word,
            'category_label' => $this->faker->word,
            'category_color' => $this->faker->hexColor,
            'percentage' => $this->faker->randomFloat(2, 0, 100),
            'total_income' => $this->faker->randomFloat(2, 0, 1000),
            'total_expense' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
