<?php

namespace App\category\Application\Command\Delete;

class DeleteCategoryCommand
{
    public function __construct(
        public string $userId,
        public string $categoryId,
    )
    {
    }
}
