<?php

namespace App\Subscription\Domain;

enum SubscriptionMessagesEnum: string
{
    const SUBSCRIPTION_SUCCESS = 'subscription_success';
    const NOT_FOUND = 'subscription_not_found';
    const SUBSCRIBER_ALREADY_SUBSCRIBED_TO_THIS_SUBSCRIPTION = 'subscriber_already_subscribed_to_this_subscription';
}
