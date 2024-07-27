<?php

namespace App\Statistics\Infrastructure\Http\Controllers;

use App\Statistics\Application\Query\MonthlyStatistics\All\GetAllMonthlyStatisticsCommand;
use App\Statistics\Application\Query\MonthlyStatistics\All\GetAllMonthlyStatisticsHandler;
use App\Statistics\Infrastructure\ViewModels\GetAllMonthlyStatisticViewModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllMonthlyStatisticsAction
{
    public function __invoke(
        GetAllMonthlyStatisticsHandler $handler,
        Request                        $request,
    ): JsonResponse
    {
        $command = new GetAllMonthlyStatisticsCommand(
            userId: $request->get('userId'),
            year: $request->get('year'),
            month: $request->get('month')
        );
        $response = $handler->handle($command);
        $data = (new GetAllMonthlyStatisticViewModel(
            selectedMonth: $command->month,
            response: $response
        ))->toArray();
        $httpJson = [
            'status' => true,
            'data' => $data
        ];
        return response()->json($httpJson);
    }
}
