<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Operation\Application\Queries\Filter\FilterAccountOperationsHandler;
use App\Operation\Infrastructure\Factories\FilterAccountOperationsCommandFactory;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Operation\Infrastructure\ViewModels\FilterAccountOperationsViewModel;
use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class FilterAccountOperationsAction
{
    public function __invoke(
        FilterAccountOperationsHandler $handler,
        Request                        $request,
        OperationsLogger               $logger,
        ChannelNotification            $channelNotification,
    ): JsonResponse
    {

        $httpJson = [
            'status' => false,
            'operations' => [],
        ];

        try {
            $command = FilterAccountOperationsCommandFactory::buildFromRequest($request);

            $response = $handler->handle($command);
            $httpJson = [
                'status' => $response->status,
                'operations' => $response->operations,
                'total' => $response->total,
                'numberOfPages' => $response->numberOfPages
            ];
            if ($command->month) {
                $operationsByDate = (new FilterAccountOperationsViewModel($response))->toArray();
                $httpJson['operationsByDate'] = $operationsByDate;
            }
            $logger->Log(
                message: 'filter account',
                level: LogLevelEnum::INFO,
                description: 'filter ',
            );
        } catch (InvalidArgumentException $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::ALERT,
                description: [
                    'userId' => $request->get('userId'),
                    'page' => $request->get('page'),
                    'limit' => $request->get('limit'),
                    'accountId' => $request->get('accountId'),
                ],
            );
            $httpJson['message'] = $e->getMessage();
        } catch (Exception $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::ERROR,
                description: $e,
            );
            $httpJson['message'] = ErrorMessagesEnum::TECHNICAL;
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'OPERATION (FILTER)',
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
