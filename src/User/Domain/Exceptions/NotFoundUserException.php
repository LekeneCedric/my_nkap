<?php

namespace App\User\Domain\Exceptions;

use App\User\Domain\Enums\UserMessagesEnum;
use Exception;

class NotFoundUserException extends Exception
{
    protected $message = UserMessagesEnum::NOT_FOUND;
}
