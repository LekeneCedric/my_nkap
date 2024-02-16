<?php

namespace App\Operation\Infrastructure\database\factories;

use App\Operation\Infrastructure\Model\Operation;
use Illuminate\Database\Eloquent\Factories\Factory;

class OperationFactory extends Factory
{
    protected $model = Operation::class;

    public function definition(): array
    {
        return [
          'uuid' => $this->faker->uuid,
          'type' => $this->faker->numberBetween(1,2),
          'amount' => $this->faker->numberBetween(1000, 100000),
          'category' => $this->faker->name,
          'details' => $this->faker->text,
          'date' => $this->faker->date('Y-m-d H:i:s'),
          'is_deleted' => false
        ];
    }
}
