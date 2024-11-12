<?php

namespace App\category\Infrastructure\Http\Controllers;

use App\category\Application\Query\all\GetAllCategoryHandler;
use App\category\Infrastructure\Logs\CategoryLogger;
use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllCategoryAction
{
    public function __invoke(
        ChannelNotification $channelNotification,
        GetAllCategoryHandler $handler,
        CategoryLogger $logger,
        string $userId,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'categories' => [],
        ];

        try {
            $response = $handler->handle(userId: $userId);
            $httpJson = [
                'status' => true,
                'categories' => $response->categories
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
                        'module' => 'Category',
                        'message' => $e->getMessage(),
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
