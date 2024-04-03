<?php

namespace App\FinancialGoal\Infrastructure\Http\Controllers;

use App\FinancialGoal\Application\Command\Delete\DeleteFinancialGoalHandler;
use App\FinancialGoal\Domain\Exceptions\NotFoundFinancialGoalException;
use App\FinancialGoal\Infrastructure\Factories\DeleteFinancialGoalCommandFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteFinancialGoalAction
{
    public function __invoke(
        DeleteFinancialGoalHandler $handler,
        Request $request,
    ): JsonResponse
    {
        $httpResponse = [
            'status' => false,
            'isDeleted' => false,
        ];

        try {
            $command = DeleteFinancialGoalCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);
            $httpResponse = [
                'status' => $response->status,
                'isDeleted' => $response->isDeleted,
            ];
        } catch (NotFoundFinancialGoalException $e) {
            $httpResponse['message'] = $e->getMessage();
        }

        return response()->json($httpResponse);
    }
}
