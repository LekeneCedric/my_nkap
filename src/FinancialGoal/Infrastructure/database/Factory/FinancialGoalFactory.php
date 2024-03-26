<?php

namespace App\FinancialGoal\Infrastructure\database\Factory;

use App\FinancialGoal\Infrastructure\Model\FinancialGoal;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialGoalFactory extends Factory
{
    protected $model = FinancialGoal::class;
    public function definition(): array
    {
       return [
           'uuid' => $this->faker->uuid,
           'start_date' => $this->faker->date('Y-m-d'),
           'end_date' => $this->faker->date('Y-m-d'),
           'details' => $this->faker->text,
           'current_amount' => 0,
           'desired_amount' => $this->faker->numberBetween(100, 100000),
           'is_complete' => false,
           'is_deleted' => false
       ];
    }
}
