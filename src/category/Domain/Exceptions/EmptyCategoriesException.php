<?php

namespace App\category\Domain\Exceptions;

use App\category\Domain\Enums\CategoryMessagesEnum;
use Exception;

class EmptyCategoriesException extends Exception
{
    protected $message = CategoryMessagesEnum::empty;
}
