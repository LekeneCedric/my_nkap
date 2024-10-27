<?php

namespace App\User\Infrastructure\Exceptions;

use App\User\Domain\Enums\UserMessagesEnum;
use Exception;

class NotFoundUserException extends Exception
{
    protected $message = UserMessagesEnum::NOT_FOUND;
}
