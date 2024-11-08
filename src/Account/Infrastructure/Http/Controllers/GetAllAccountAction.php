<?php

namespace App\Account\Infrastructure\Http\Controllers;

use App\Account\Application\Queries\All\GetAllAccountHandler;
use App\Account\Domain\Exceptions\ErrorOnGetAllAccountException;
use App\Account\Infrastructure\Logs\AccountLogger;
use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Exception;
use Illuminate\Http\JsonResponse;

class GetAllAccountAction
{
    public function __invoke(
        GetAllAccountHandler $handler,
        string               $userId,
        AccountLogger        $logger,
        ChannelNotification $channelNotification,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'accounts' => []
        ];

        try {
            $response = $handler->handle(userId: $userId);

            $httpJson = [
                'status' => $response->status,
                'accounts' => $response->accounts,
            ];
        } catch (Exception $e) {
            $file = $e->getFile();
            $line = $e->getLine();
            $httpJson['message'] = ErrorMessagesEnum::TECHNICAL;
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::CRITICAL,
                description: $e,
            );
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'Accounts',
                        'level' => ErrorLevelEnum::CRITICAL->value,
                        'command' => json_encode(['userId' => $userId], JSON_PRETTY_PRINT),
                        'trace' => "Error in file: $file on line: $line"
                    ],
                )
            );
        }

        return response()->json($httpJson);
    }
}
