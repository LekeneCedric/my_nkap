<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalCommand;
use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalHandler;
use App\Operation\Application\Command\DeleteOperation\DeleteOperationHandler;
use App\Operation\Domain\Exceptions\NotFoundOperationException;
use App\Operation\Infrastructure\Factories\DeleteOperationCommandFactory;
use App\Operation\Infrastructure\Http\Requests\DeleteOperationRequest;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Exception;
use Illuminate\Http\JsonResponse;

class DeleteOperationAction
{
    public function __invoke(
        DeleteOperationHandler     $handler,
        UpdateFinancialGoalHandler $updateFinancialGoalHandler,
        DeleteOperationRequest     $request,
        OperationsLogger           $logger,
    ): JsonResponse
    {
        $httpJson = ['status' => false];

        try {
            $command = DeleteOperationCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);

            $updateFinancialGoalCommand = new UpdateFinancialGoalCommand(
                accountId: $command->accountId,
                previousAmount: $response->operationAmount,
                newAmount: $response->operationAmount,
                operationDate: $response->date,
                type: $response->operationType,
                isDelete: true,
            );
            $updateFinancialGoalHandler->handle($updateFinancialGoalCommand);
            $httpJson = [
                'status' => true,
                'isDeleted' => $response->isDeleted,
                'message' => $response->message,
                'operationId' => $command->operationId,
            ];
        } catch (
        NotFoundAccountException|
        NotFoundOperationException $e
        ) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::WARNING,
                description: $e,
            );
            $httpJson['message'] = $e->getMessage();
        } catch (Exception $e) {
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
