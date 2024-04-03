<?php

namespace App\User\Infrastructure\database\Factories;

use App\User\Infrastructure\Models\Profession;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionFactory extends Factory
{
    protected $model = Profession::class;
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->name,
        ];
    }
}
