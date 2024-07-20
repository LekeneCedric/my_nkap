<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalCommand;
use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalHandler;
use App\Operation\Application\Command\MakeOperation\MakeOperationHandler;
use App\Operation\Domain\Exceptions\OperationGreaterThanAccountBalanceException;
use App\Operation\Infrastructure\Factories\MakeOperationCommandFactory;
use App\Operation\Infrastructure\Http\Requests\MakeOperationRequest;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Illuminate\Http\JsonResponse;

class MakeOperationAction
{
    public function __invoke(
        MakeOperationHandler $handler,
        UpdateFinancialGoalHandler $updateFinancialGoalHandler,
        MakeOperationRequest $request,
        OperationsLogger $logger,
    ): JsonResponse
    {
        $httpJson = ['status' => false, 'operationIsSaved' => false];

        try {
            $command = MakeOperationCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);

            $updateFinancialGoalCommand = new UpdateFinancialGoalCommand(
                accountId: $command->accountId,
                previousAmount: $response->previousOperationAmount,
                amount: $command->amount,
                operationDate: $command->date
            );
            $updateFinancialGoalHandler->handle($updateFinancialGoalCommand);

            $httpJson = [
                'status' => true,
                'operationSaved' => $response->operationSaved,
                'operationId' => $response->operationId,
            ];
        } catch (
            NotFoundAccountException|
            OperationGreaterThanAccountBalanceException $e
        ) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::CRITICAL,
                description: $e,
            );
            $httpJson['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::ERROR,
                description: $e,
            );
            $httpJson['message'] = 'Une erreur est survenue lors du traitement de votre requête , veuillez réessayer ultérieurement !';
        }

        return response()->json($httpJson);
    }
}
