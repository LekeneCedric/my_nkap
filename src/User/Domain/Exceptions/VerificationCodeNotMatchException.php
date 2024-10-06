<?php

namespace App\User\Domain\Exceptions;

use App\User\Domain\Enums\UserMessagesEnum;
use Exception;

class VerificationCodeNotMatchException extends Exception
{
    protected $message = UserMessagesEnum::VERIFICATION_CODE_NOT_MATCH;
}
