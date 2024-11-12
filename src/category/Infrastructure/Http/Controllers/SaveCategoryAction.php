<?php

namespace App\category\Infrastructure\Http\Controllers;

use App\category\Application\Command\Save\SaveCategoryHandler;
use App\category\Domain\Exceptions\AlreadyExistsCategoryException;
use App\category\Domain\Exceptions\NotFoundCategoryException;
use App\category\Domain\Exceptions\NotFoundUserCategoryException;
use App\category\Infrastructure\Factories\SaveCategoryCommandFactory;
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

class SaveCategoryAction
{
    public function __invoke(
        SaveCategoryHandler $handler,
        Request             $request,
        CategoryLogger      $logger,
        ChannelNotification $channelNotification,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'isSaved' => false,
        ];

        try {
            $command = SaveCategoryCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);
            $httpJson = [
                'status' => true,
                'isSaved' => $response->isSaved,
                'message' => $response->message,
                'categoryId' => $response->categoryId,
            ];
        } catch (AlreadyExistsCategoryException $e) {
            $httpJson['message'] = $e->getMessage();
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::INFO,
                description: $e,
            );
        } catch (
        NotFoundCategoryException
        |NotFoundUserCategoryException  $e) {
            $file = $e->getFile();
            $line = $e->getLine();
            $httpJson['message'] = $e->getMessage();
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
                        'trace' => "Error in file: $file on line: $line"
                    ],
                )
            );
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
