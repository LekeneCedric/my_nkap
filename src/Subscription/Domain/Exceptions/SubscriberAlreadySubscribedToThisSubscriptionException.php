<?php

namespace App\Subscription\Domain\Exceptions;

use App\Subscription\Domain\SubscriptionMessagesEnum;
use Exception;

class SubscriberAlreadySubscribedToThisSubscriptionException extends Exception
{
    protected $message = SubscriptionMessagesEnum::SUBSCRIBER_ALREADY_SUBSCRIBED_TO_THIS_SUBSCRIPTION;
}
