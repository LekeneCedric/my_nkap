<?php

namespace App\category\Domain;

use App\category\Domain\Enums\EventState\CategoryEventStateEnum;
use App\category\Domain\Exceptions\AlreadyExistsCategoryException;
use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;

class UserCategory
{
    private ?Category $currentCategory;

    /**
     * @param Id $id
     * @param Category[] $categories
     */
    public function __construct(
        public readonly Id $id,
        public readonly array $categories,
    )
    {
        $this->currentCategory = null;
    }

    public function currentCategory(): ?Category
    {
        return $this->currentCategory;
    }

    /**
     * @param StringVO $icon
     * @param StringVO $name
     * @param StringVO $color
     * @param StringVO $description
     * @return void
     * @throws AlreadyExistsCategoryException
     */
    public function addCategory(
        StringVO $icon,
        StringVO $name,
        StringVO $color,
        StringVO $description
    ): void
    {
        $this->checkIfNotAlreadyExistSameCategoryOrThrowException(name: $name);
        $this->currentCategory = Category::create(
            icon: $icon,
            name: $name,
            color: $color,
            description: $description
        );
    }

    /**
     * @param StringVO $icon
     * @param StringVO $name
     * @param StringVO $color
     * @param StringVO $description
     * @param Id $id
     * @return void
     * @throws NotFoundCategoryException
     */
    public function updateCategory(
        StringVO $icon,
        StringVO $name,
        StringVO $color,
        StringVO $description,
        Id $id
    ): void
    {
        $this->checkIfAlreadyExistCategoryOrThrowException($id);
        $this->currentCategory = Category::create(
            icon: $icon,
            name: $name,
            color: $color,
            description: $description,
            id: $id
        );
    }

    /**
     * @param Id $categoryId
     * @return void
     * @throws NotFoundCategoryException
     */
    public function deleteCategory(Id $categoryId): void
    {
        array_map(function(Category $category) use ($categoryId) {
            if ($category->categoryId->value() === $categoryId->value()) {
                $this->currentCategory = $category;
                $this->currentCategory->changeEventState(CategoryEventStateEnum::onDelete);
            }
        }, $this->categories);
        if (is_null($this->currentCategory)) {
            throw new NotFoundCategoryException();
        }
    }

    /**
     * @param StringVO $name
     * @return void
     * @throws AlreadyExistsCategoryException
     */
    private function checkIfNotAlreadyExistSameCategoryOrThrowException(StringVO $name): void
    {
        foreach ($this->categories as $category) {
            if ($category->name->value() === $name->value()) {
                throw new AlreadyExistsCategoryException();
            }
        }
    }

    /**
     * @param Id $id
     * @return void
     * @throws NotFoundCategoryException
     */
    private function checkIfAlreadyExistCategoryOrThrowException(Id $id): void
    {
        foreach ($this->categories as $category) {
            if ($category->categoryId->value() === $id->value()) {
                return ;
            }
        }
        throw new NotFoundCategoryException();
    }
}
