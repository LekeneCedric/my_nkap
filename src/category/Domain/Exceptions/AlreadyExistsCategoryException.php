<?php

namespace App\category\Domain\Exceptions;

use App\category\Domain\Enums\CategoryMessagesEnum;
use Exception;

class AlreadyExistsCategoryException extends Exception
{
    protected $message = CategoryMessagesEnum::ALREADY_EXISTS;
}
