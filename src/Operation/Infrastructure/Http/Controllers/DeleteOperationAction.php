<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Account\Domain\Exceptions\NotFoundAccountException;
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
        DeleteOperationHandler $handler,
        DeleteOperationRequest $request,
        OperationsLogger $logger,
    ): JsonResponse
    {
        $httpJson = ['status' => false];

        try {
            $command = DeleteOperationCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);

            $httpJson = [
                'status' => true,
                'isDeleted' => $response->isDeleted,
                'message' => $response->message,
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
