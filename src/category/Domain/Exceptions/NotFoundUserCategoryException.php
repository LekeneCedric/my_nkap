<?php

namespace App\category\Domain\Exceptions;

use App\User\Domain\Enums\UserMessagesEnum;
use Exception;

class NotFoundUserCategoryException extends Exception
{
    protected $message = UserMessagesEnum::NOT_FOUND;
}
