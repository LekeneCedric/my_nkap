<?php

namespace App\category\Infrastructure\database\Factories;

use App\category\Infrastructure\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'user_id' => 1,
            'icon' => $this->faker->name,
            'name' => $this->faker->name,
            'description' => $this->faker->text(100)
        ];
    }
}
