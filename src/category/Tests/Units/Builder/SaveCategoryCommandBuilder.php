<?php

namespace App\category\Tests\Units\Builder;

use App\category\Application\Command\Save\SaveCategoryCommand;

class SaveCategoryCommandBuilder
{
    private ?string $userId;
    private ?string $categoryId;
    private ?string $categoryIcon;
    private ?string $categoryName;
    private ?string $categoryDescription;
    public static function asCommand(): SaveCategoryCommandBuilder
    {
        $self = new self();
        $self->userId = null;
        $self->categoryId = null;
        $self->categoryIcon = null;
        $self->categoryName = null;
        $self->categoryDescription = null;
        return $self;
    }

    /**
     * @param string $userId
     * @return $this
     */
    public function withUserId(string $userId): static
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @param string $categoryIcon
     * @return $this
     */
    public function withCategoryIcon(string $categoryIcon): static
    {
        $this->categoryIcon = $categoryIcon;
        return $this;
    }

    /**
     * @param string $categoryName
     * @return $this
     */
    public function withCategoryName(string $categoryName): static
    {
        $this->categoryName = $categoryName;
        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function withCategoryDescription(string $description): static
    {
        $this->categoryDescription = $description;
        return $this;
    }

    /**
     * @param string $categoryId
     * @return $this
     */
    public function withCategoryId(string $categoryId): static
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function build(): SaveCategoryCommand
    {
        return new SaveCategoryCommand(
            userId: $this->userId,
            categoryIcon: $this->categoryIcon,
            categoryName: $this->categoryName,
            categoryDescription: $this->categoryDescription,
            categoryId: $this->categoryId,
        );
    }
}
