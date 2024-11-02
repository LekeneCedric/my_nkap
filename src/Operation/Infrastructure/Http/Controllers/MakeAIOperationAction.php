<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\category\Domain\Exceptions\EmptyCategoriesException;
use App\Operation\Application\Command\MakeAIOperation\MakeAIOperationHandler;
use App\Operation\Domain\Exceptions\AIOperationEmptyMessageException;
use App\Operation\Infrastructure\Factories\MakeAIOperationCommandFactory;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use App\User\Domain\Exceptions\NotFoundUserException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class MakeAIOperationAction
{
    public function __invoke(
        MakeAIOperationHandler $handler,
        Request $request,
        OperationsLogger $logger,
        ChannelNotification $channelNotification,
    ): JsonResponse
    {
        $httpResponse = [
            'status' => false,
            'operationIsOk' => false,
            'message' => '',
            'consumedToken' => 0,
            'operations' => [],
        ];
        try {
            $command = MakeAIOperationCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);
            $httpResponse = [
                'status' => true,
                'operationIsOk' => $response->operationOk,
                'message' => $response->message,
                'consumedToken' => $response->consumedToken,
                'operations' => $response->operations
            ];
        } catch (
            AIOperationEmptyMessageException
            |NotFoundUserException
            |EmptyCategoriesException $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::WARNING,
                description: $e,
            );
            $httpResponse['message'] = $e->getMessage();
        } catch (InvalidArgumentException $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::ERROR,
                description: $e,
            );
            $httpResponse['message'] = ErrorMessagesEnum::TECHNICAL;
        } catch (Exception $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::CRITICAL,
                description: $e,
            );
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'AI-OPERATION',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::CRITICAL->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => $e->getTraceAsString()
                    ],
                )
            );
            $httpResponse['message'] = ErrorMessagesEnum::TECHNICAL;
        }

        return response()->json($httpResponse);
    }
}
