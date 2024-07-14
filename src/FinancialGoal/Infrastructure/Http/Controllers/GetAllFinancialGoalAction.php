<?php

namespace App\FinancialGoal\Infrastructure\Http\Controllers;

use App\FinancialGoal\Application\Query\All\GetAllFinancialGoalHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllFinancialGoalAction
{
    public function __invoke(
        GetAllFinancialGoalHandler $handler,
        Request $request,
    ): JsonResponse
    {
        $userId = $request->get('userId');
        $response = $handler->handle($userId);
        $httpResponse = [
            'status' => true,
            'financialGoals' => $response->financialGoals,
        ];
        return response()->json($httpResponse);
    }
}
