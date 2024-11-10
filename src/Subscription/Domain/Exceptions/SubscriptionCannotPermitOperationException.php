<?php

namespace App\Subscription\Domain\Exceptions;

use App\Subscription\Domain\Enums\SubscriptionMessagesEnum;
use Exception;

class SubscriptionCannotPermitOperationException extends Exception
{
    protected $message = SubscriptionMessagesEnum::CANNOT_MAKE_OPERATION;
}
