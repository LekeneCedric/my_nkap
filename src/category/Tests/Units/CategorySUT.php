<?php

namespace App\category\Tests\Units;

use App\category\Domain\Category;
use App\category\Domain\UserCategory;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;

class CategorySUT
{
    private array $categories;
    public UserCategory $user;

    public static function asSUT(): CategorySUT
    {
        $self = new self();
        $self->categories = [];
        return $self;
    }

    public function withExistingUser(): static
    {
        $this->user = new UserCategory(
            id: new Id(),
            categories: [],
        );
        return $this;
    }

    public function withExistingCategory(
        string $icon,
        string $name,
        string $description
    ): static
    {
        $this->categories[] = new Category(
            categoryId: new Id(),
            icon: new StringVO($icon),
            name: new StringVO($name),
            description: new StringVO($description)
        );
        return $this;
    }

    public function build(): static
    {
        if (count($this->categories) > 0) {
            $this->user = new UserCategory(
                id: new Id(),
                categories: $this->categories,
            );
        }
        return $this;
    }
}
