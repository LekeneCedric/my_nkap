<?php

namespace App\category\Domain\Exceptions;

use App\category\Domain\Enums\CategoryMessagesEnum;

class NotFoundCategoryException extends \Exception
{
    protected $message = CategoryMessagesEnum::NOT_FOUND;
}
