<?php

namespace App\Statistics\Infrastructure\Http\Controllers;

use App\Statistics\Application\Query\MonthlyCategoryStatistics\All\GetAllMonthlyCategoryStatisticsCommand;
use App\Statistics\Application\Query\MonthlyCategoryStatistics\All\GetAllMonthlyCategoryStatisticsHandler;
use App\Statistics\Infrastructure\ViewModels\GetAllMonthlyCategoryStatisticViewModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllMonthlyCategoryStatisticsAction
{
    public function __invoke(
        GetAllMonthlyCategoryStatisticsHandler $handler,
        Request                        $request,
    ): JsonResponse
    {
        $command = new GetAllMonthlyCategoryStatisticsCommand(
            userId: $request->get('userId'),
            year: $request->get('year'),
            month: $request->get('month')
        );
        $response = $handler->handle($command);
        $data = (new GetAllMonthlyCategoryStatisticViewModel(
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
