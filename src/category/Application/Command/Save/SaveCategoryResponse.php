<?php

namespace App\category\Application\Command\Save;

class SaveCategoryResponse
{
    public bool $isSaved = false;
    public string $categoryId = '';
    public string $message = '';
}
