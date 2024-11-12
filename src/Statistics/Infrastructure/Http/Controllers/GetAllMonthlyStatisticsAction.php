<?php

namespace App\Statistics\Infrastructure\Http\Controllers;

use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Statistics\Application\Query\MonthlyStatistics\All\GetAllMonthlyStatisticsCommand;
use App\Statistics\Application\Query\MonthlyStatistics\All\GetAllMonthlyStatisticsHandler;
use App\Statistics\Infrastructure\ViewModels\GetAllMonthlyStatisticViewModel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllMonthlyStatisticsAction
{
    public function __invoke(
        GetAllMonthlyStatisticsHandler $handler,
        Request                        $request,
        ChannelNotification            $channelNotification,
    ): JsonResponse
    {
        $httpJson = [
            'status' => true,
            'data' => [],
        ];
        $command = new GetAllMonthlyStatisticsCommand(
            userId: $request->get('userId'),
            year: $request->get('year'),
            month: $request->get('month')
        );
        try {
            $response = $handler->handle($command);
            $data = (new GetAllMonthlyStatisticViewModel(
                selectedMonth: $command->month,
                response: $response
            ))->toArray();
            $httpJson = [
                'status' => true,
                'data' => $data
            ];
        } catch (Exception $e) {
            $file = $e->getFile();
            $line = $e->getLine();
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'STATISTICS (GET-MONTHLY-BY-CATEGORY)',
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
