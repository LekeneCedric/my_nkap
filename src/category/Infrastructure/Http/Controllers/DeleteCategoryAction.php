<?php

namespace App\category\Infrastructure\Http\Controllers;

use App\category\Application\Command\Delete\DeleteCategoryCommand;
use App\category\Application\Command\Delete\DeleteCategoryHandler;
use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\category\Domain\Exceptions\NotFoundUserCategoryException;
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

class DeleteCategoryAction
{
    public function __invoke(
        ChannelNotification $channelNotification,
        DeleteCategoryHandler $handler,
        Request               $request,
        CategoryLogger        $logger,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'isDeleted' => false,
        ];

        try {
            $command = new DeleteCategoryCommand(
                userId: $request->get('userId'),
                categoryId: $request->get('categoryId')
            );
            $response = $handler->handle($command);
            $httpJson = [
                'status' => true,
                'isDeleted' => $response->isDeleted,
                'message' => $response->message,
            ];
        } catch (
        NotFoundUserCategoryException|
        NotFoundCategoryException $e) {
            $httpJson['message'] = ErrorMessagesEnum::TECHNICAL;
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::ERROR,
                description: $e,
            );
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'Category',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::WARNING->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => $e->getTraceAsString()
                    ],
                )
            );
        } catch (Exception $e) {
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
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => $e->getTraceAsString()
                    ],
                )
            );
        }

        return response()->json($httpJson);
    }
}
