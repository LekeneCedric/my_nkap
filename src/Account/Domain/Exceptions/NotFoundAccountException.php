<?php

namespace App\Account\Domain\Exceptions;

use App\Account\Domain\Enums\AccountMessagesEnum;

class NotFoundAccountException extends \Exception
{
    protected $message = AccountMessagesEnum::NOT_FOUND;
}
