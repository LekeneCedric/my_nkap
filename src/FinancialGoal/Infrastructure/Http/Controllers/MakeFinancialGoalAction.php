<?php

namespace App\FinancialGoal\Infrastructure\Http\Controllers;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\FinancialGoal\Application\Command\Make\MakeFinancialGoalHandler;
use App\FinancialGoal\Domain\Exceptions\ErrorOnSaveFinancialGoalException;
use App\FinancialGoal\Infrastructure\Factories\MakeFinancialGoalCommandFactory;
use App\FinancialGoal\Infrastructure\Logs\FinancialGoalLogger;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use App\User\Domain\Exceptions\NotFoundUserException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MakeFinancialGoalAction
{
    public function __invoke(
        MakeFinancialGoalHandler $handler,
        Request $request,
        FinancialGoalLogger $logger,
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
        } catch (NotFoundAccountException|NotFoundUserException $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::WARNING,
                description: json_encode([
                    'message' => $e->getMessage(),
                    'exception' => $e,
                    'command' => $command,
                ], JSON_PRETTY_PRINT),
            );
            $httpResponse['message'] = $e->getMessage();
        } catch (ErrorOnSaveFinancialGoalException $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::CRITICAL,
                description: json_encode([
                    'message' => $e->getMessage(),
                    'exception' => $e,
                    'command' => $command,
                ], JSON_PRETTY_PRINT),
            );
            $httpResponse['message'] = 'Une érreur critique est survenue lors du traitement de votre opération , veuillez réessayer plus-târd !';
        } catch (Exception $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::ERROR,
                description: json_encode([
                    'message' => $e->getMessage(),
                    'exception' => $e,
                    'command' => $command,
                ], JSON_PRETTY_PRINT),
            );
            $httpResponse['message'] = 'Une érreur est survenue lors du traitement de votre requête , veuillez réessayer plus-târd!';
        }

        return response()->json($httpResponse);
    }
}
