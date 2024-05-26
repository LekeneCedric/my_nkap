<?php

namespace App\category\Tests\Features;

use App\category\Domain\Category;
use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\category\Infrastructure\Models\Category as CategoryModel;
use App\category\Domain\Exceptions\AlreadyExistsCategoryException;
use App\category\Domain\UserCategory;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;

class CategorySUT
{
    private User $user;
    /**
     * @var Category[]
     */
    private array $categories;
    private ?CategoryModel $existingCategory;
    private ?Category $toAddCategory;
    private ?Category $toUpdateCategory;
    public ?UserCategory $userCategory;

    public static function asSUT(): CategorySUT
    {
        $self = new self();
        $self->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => 'user@gmail.com',
            'name' => 'user_237',
            'password' => bcrypt('user@5144'),
            'profession_id' => (Profession::factory()->create())->id,
        ]);
        $self->categories = [];
        $self->userCategory = null;
        $self->existingCategory = null;
        $self->toAddCategory = null;
        $self->toUpdateCategory = null;
        return $self;
    }

    public function withToAddCategory(
        string $icon,
        string $name,
        string $description
    ): static
    {
        $this->toAddCategory = Category::create(
            icon: new StringVO($icon),
            name: new StringVO($name),
            description: new StringVO($description),
        );
        return $this;
    }

    public function withExistingCategory(
        string $icon,
        string $name,
        string $description
    ): static
    {
        $this->existingCategory = CategoryModel::factory()->create([
            'icon' => $icon,
            'name' => $name,
            'description' => $description,
            'user_id' => $this->user->id,
        ]);
        $this->categories[] = new Category(
            categoryId: new Id($this->existingCategory->uuid),
            icon: new StringVO($this->existingCategory->icon),
            name: new StringVO($this->existingCategory->name),
            description: new StringVO($this->existingCategory->description),
        );
        return $this;
    }

    public function withToUpdateCategory(
        string $icon,
        string $name,
        string $description
    ): static
    {
        $this->toUpdateCategory = Category::create(
            icon: new StringVO($icon),
            name: new StringVO($name),
            description: new StringVO($description),
            id: new Id($this->existingCategory->uuid)
        );
        return $this;
    }

    /**
     * @return $this
     * @throws AlreadyExistsCategoryException
     * @throws NotFoundCategoryException
     */
    public function build(): static
    {
        $this->userCategory = new UserCategory(
            id: new Id($this->user->uuid),
            categories: $this->categories,
        );
        if ($this->toAddCategory) {
            $this->userCategory->addCategory(
                icon: $this->toAddCategory->icon,
                name: $this->toAddCategory->name,
                description: $this->toAddCategory->description,
            );
        }
        if ($this->toUpdateCategory) {
            $this->userCategory->updateCategory(
                icon: $this->toUpdateCategory->icon,
                name: $this->toUpdateCategory->name,
                description: $this->toUpdateCategory->description,
                id: $this->toUpdateCategory->categoryId,
            );
        }
        return $this;
    }
}
