<?php

namespace App\Subscription\Infrastructure\Http\Controllers;

use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use App\Subscription\Application\Query\all\GetAllSubscriptionCommand;
use App\Subscription\Application\Query\all\GetAllSubscriptionHandler;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllSubscriptionAction
{
    public function __invoke(
        GetAllSubscriptionHandler $handler,
        Request                  $request,
        ChannelNotification      $channelNotification,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'subscriptions' => [],
            'message' => '',
        ];

        try {
            $command = new GetAllSubscriptionCommand(
                userId: $request->get('userId'),
            );
            $response = $handler->handle($command);
            $httpJson = [
                'status' => true,
                'subscriptions' => $response->subscriptions,
            ];
        } catch (Exception $e) {
            $file = $e->getFile();
            $line = $e->getLine();
            $httpJson['message'] = ErrorMessagesEnum::TECHNICAL;
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'Accounts',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::CRITICAL->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => "Error in file: $file on line: $line"
                    ],
                )
            );
        }
        return response()->json($httpJson);
    }
}
