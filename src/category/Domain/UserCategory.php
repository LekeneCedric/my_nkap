<?php

namespace App\category\Domain;

use App\category\Domain\Exceptions\AlreadyExistsCategoryException;
use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;

class UserCategory
{
    private ?Category $currentCategory;
    private ?DateVO $updatedAt;

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
        $this->updatedAt = null;
    }

    public function currentCategory(): ?Category
    {
        return $this->currentCategory;
    }

    /**
     * @param StringVO $icon
     * @param StringVO $name
     * @param StringVO $description
     * @return void
     * @throws AlreadyExistsCategoryException
     */
    public function addCategory(
        StringVO $icon,
        StringVO $name,
        StringVO $description
    ): void
    {
        $this->checkIfNotAlreadyExistSameCategoryOrThrowException(name: $name);
        $this->currentCategory = Category::create(
            icon: $icon,
            name: $name,
            description: $description
        );
        $this->updatedAt = new DateVO();
    }

    /**
     * @param StringVO $icon
     * @param StringVO $name
     * @param StringVO $description
     * @param Id $id
     * @return void
     * @throws NotFoundCategoryException
     */
    public function updateCategory(
        StringVO $icon,
        StringVO $name,
        StringVO $description,
        Id $id
    ): void
    {
        $this->checkIfAlreadyExistCategoryOrThrowException($id);
        $this->currentCategory = Category::create(
            icon: $icon,
            name: $name,
            description: $description,
            id: $id
        );
        $this->updatedAt = new DateVO();
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
