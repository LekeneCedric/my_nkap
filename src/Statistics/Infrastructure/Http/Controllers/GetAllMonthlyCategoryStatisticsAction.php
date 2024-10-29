<?php

namespace App\Statistics\Infrastructure\Http\Controllers;

use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Statistics\Application\Query\MonthlyCategoryStatistics\All\GetAllMonthlyCategoryStatisticsCommand;
use App\Statistics\Application\Query\MonthlyCategoryStatistics\All\GetAllMonthlyCategoryStatisticsHandler;
use App\Statistics\Infrastructure\ViewModels\GetAllMonthlyCategoryStatisticViewModel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllMonthlyCategoryStatisticsAction
{
    public function __invoke(
        GetAllMonthlyCategoryStatisticsHandler $handler,
        Request                                $request,
        ChannelNotification                    $channelNotification,
    ): JsonResponse
    {
        $httpJson = [
            'status' => true,
            'data' => [],
        ];
        $command = new GetAllMonthlyCategoryStatisticsCommand(
            userId: $request->get('userId'),
            year: $request->get('year'),
            month: $request->get('month')
        );
        try {
            $response = $handler->handle($command);
            $data = (new GetAllMonthlyCategoryStatisticViewModel(
                selectedMonth: $command->month,
                response: $response
            ))->toArray();
            $httpJson = [
                'status' => true,
                'data' => $data
            ];
        } catch (Exception $e) {
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'STATISTICS (GET-MONTHLY-BY-CATEGORY)',
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
