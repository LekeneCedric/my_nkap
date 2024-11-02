<?php

namespace App\User\Domain\Exceptions;

use App\User\Domain\Enums\UserMessagesEnum;
use Exception;

class AlreadyUserExistWithSameEmailException extends Exception
{
    protected $message = UserMessagesEnum::ALREADY_EXIST_WITH_SAME_EMAIL;
}
