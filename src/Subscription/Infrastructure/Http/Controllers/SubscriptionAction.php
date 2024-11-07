<?php

namespace App\Subscription\Infrastructure\Http\Controllers;

use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use App\Subscription\Application\Command\Subscribe\SubscriptionHandler;
use App\Subscription\Domain\Exceptions\NotFoundSubscriptionException;
use App\Subscription\Domain\Exceptions\SubscriberAlreadySubscribedToThisSubscriptionException;
use App\Subscription\Infrastructure\Factories\SubscriptionCommandFactory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionAction
{
    public function __invoke(
        SubscriptionHandler $handler,
        Request             $request,
        ChannelNotification $channelNotification,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'isSubscribed' => false,
            'nb_token_per_day' => 0,
            'nb_operations_per_day' => 0,
        ];

        try {
            $command = SubscriptionCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);
            $httpJson = [
                'status' => true,
                'isSubscribed' => $response->isSubscribed,
                'nb_token_per_day' => $response->subscriptionNbTokenPerDay,
                'nb_operations_per_day' => $response->subscriptionNbOperationsPerDay,
            ];
        } catch (
        NotFoundSubscriptionException|
        SubscriberAlreadySubscribedToThisSubscriptionException $e) {
            $httpJson['message'] = $e->getMessage();
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'Subscription',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::INFO->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => $e->getTraceAsString()
                    ],
                )
            );
        } catch (Exception $e) {
            $httpJson['message'] = ErrorMessagesEnum::TECHNICAL;
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'Accounts',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::CRITICAL->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => $e->getTraceAsString()
                    ],
                )
            );
        }
        return response()->json($httpJson);
    }
}
