<?php

namespace App\FinancialGoal\Infrastructure\Http\Controllers;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\FinancialGoal\Application\Command\Make\MakeFinancialGoalHandler;
use App\FinancialGoal\Domain\Exceptions\ErrorOnSaveFinancialGoalException;
use App\FinancialGoal\Infrastructure\Factories\MakeFinancialGoalCommandFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MakeFinancialGoalAction
{
    public function __invoke(
        MakeFinancialGoalHandler $handler,
        Request $request,
    ): JsonResponse
    {
        $httpResponse = [
            'status' => false
        ];

        try {
            $command = MakeFinancialGoalCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);

            $httpResponse = [
                'status' => $response->status,
                'isMake' => $response->isMake,
                'message' => $response->message,
            ];
            if (!$command->financialGoalId) {
                $httpResponse['createdAt'] = $response->createdAt;
                $httpResponse['financialGoalId'] = $response->financialGoalId;
            }
        } catch (NotFoundAccountException $e) {
            $httpResponse['message'] = $e->getMessage();
        } catch (ErrorOnSaveFinancialGoalException $e) {
            $httpResponse['message'] = 'Une érreur critique est survenue lors du traitement de votre opération , veuillez réessayer plus-târd !';
        } catch (\Exception $e) {
            $httpResponse['message'] = 'Une érreur est survenue lors du traitement de votre requête , veuillez réessayer !';
        }

        return response()->json($httpResponse);
    }
}
