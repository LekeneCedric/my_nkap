<?php

namespace App\Statistics\Infrastructure\database\factories;

use App\Shared\Domain\Enums\MonthEnum;
use App\Statistics\Infrastructure\Model\MonthlyCategoryStatistic;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonthlyCategoryStatisticFactory extends Factory
{
    protected $model = MonthlyCategoryStatistic::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'composed_id' => $this->faker->uuid,
            'user_id' => $this->faker->uuid,
            'month' => $this->faker->randomElement(MonthEnum::values()),
            'year' => $this->faker->year,
            'category_id' => $this->faker->uuid,
            'category_icon' => $this->faker->word,
            'category_label' => $this->faker->word,
            'category_color' => $this->faker->hexColor,
            'total_income' => $this->faker->randomFloat(2, 0, 1000),
            'total_expense' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
