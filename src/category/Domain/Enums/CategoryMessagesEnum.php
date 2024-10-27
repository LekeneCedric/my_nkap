<?php

namespace App\category\Domain\Enums;

enum CategoryMessagesEnum
{
    const UPDATED = 'category-updated';
    const CREATED = 'category-created';
    const ALREADY_EXISTS = 'category_already_exists';
    const NOT_FOUND = 'category_not_found';
}
