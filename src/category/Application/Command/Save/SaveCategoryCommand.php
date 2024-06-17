<?php

namespace App\category\Application\Command\Save;

class SaveCategoryCommand
{
    public function __construct(
        public string $userId,
        public string $categoryColor,
        public string $categoryIcon,
        public string $categoryName,
        public string $categoryDescription,
        public ?string $categoryId = null,
    )
    {
    }
}
