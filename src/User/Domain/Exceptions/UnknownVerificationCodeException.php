<?php

namespace App\User\Domain\Exceptions;

use App\User\Domain\Enums\UserMessagesEnum;
use Exception;

class UnknownVerificationCodeException extends Exception
{
    protected $message = UserMessagesEnum::UNKNOWN_VERIFICATION_CODE;
}
