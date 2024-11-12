<?php

namespace App\Subscription\Domain\Exceptions;

use App\Subscription\Domain\Enums\SubscriptionMessagesEnum;
use Exception;

class SubscriptionCannotPermitAccountCreationException extends Exception
{
    protected $message = SubscriptionMessagesEnum::CANNOT_CREATE_ACCOUNT;
}
