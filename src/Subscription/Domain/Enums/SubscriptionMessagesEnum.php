<?php

namespace App\Subscription\Domain\Enums;

enum SubscriptionMessagesEnum: string
{
    const SUBSCRIPTION_SUCCESS = 'subscription_success';
    const NOT_FOUND = 'subscription_not_found';
    const SUBSCRIBER_ALREADY_SUBSCRIBED_TO_THIS_SUBSCRIPTION = 'subscriber_already_subscribed_to_this_subscription';
    const CANNOT_CREATE_ACCOUNT = 'subscription_cannot_create_account';
    const CANNOT_MAKE_OPERATION = 'subscription_cannot-make_operation';
}
