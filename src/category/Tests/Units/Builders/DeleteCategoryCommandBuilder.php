<?php

namespace App\category\Tests\Units\Builders;

use App\category\Application\Command\Delete\DeleteCategoryCommand;

class DeleteCategoryCommandBuilder
{
    private ?string $userId;
    private ?string $categoryId;
    public static function asCommand(): DeleteCategoryCommandBuilder
    {
        $self = new self();
        $self->userId = null;
        return $self;
    }

    public function withUserId(string $userId): static
    {
        $this->userId = $userId;
        return $this;
    }

    public function withCategoryId(?string $categoryId): static
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function build(): DeleteCategoryCommand
    {
        return new DeleteCategoryCommand(
            userId: $this->userId,
            categoryId: $this->categoryId,
        );
    }
}
