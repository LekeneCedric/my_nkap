<?php

namespace App\category\Tests\e2e;

use App\category\Infrastructure\Models\Category;

class CategorySUT
{
    public ?Category $category;
    public static function asSUT(): CategorySUT
    {
        $self = new self();
        $self->category = null;
        return $self;
    }

    public function withExistingCategory(
        int $user_id,
        string $icon,
        string $name,
        string $description
    ): static
    {
        $this->category = Category::factory()->create([
            'user_id' => $user_id,
            'icon' => $icon,
            'name' => $name,
            'description' => $description
        ]);
        return $this;
    }

    public function build(): static
    {
        return $this;
    }
}
