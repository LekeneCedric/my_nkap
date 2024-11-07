<?php

namespace App\Subscription\Domain\Exceptions;

use App\Subscription\Domain\SubscriptionMessagesEnum;
use Exception;

class NotFoundSubscriptionException extends Exception
{
    protected $message = SubscriptionMessagesEnum::NOT_FOUND;
}
