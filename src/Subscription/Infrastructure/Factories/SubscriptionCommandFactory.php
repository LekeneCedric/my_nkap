<?php

namespace App\Subscription\Infrastructure\Factories;

use App\Subscription\Application\Command\Subscribe\SubscriptionCommand;
use Illuminate\Http\Request;

class SubscriptionCommandFactory
{
    /**
     * @param Request $request
     * @return SubscriptionCommand
     */
    public static function buildFromRequest(Request $request): SubscriptionCommand
    {
        return new SubscriptionCommand(
            userId: $request->get('userId'),
            subscriptionId: $request->get('subscriptionId'),
            nbMonth: $request->get('nbMonth'),
        );
    }
}
