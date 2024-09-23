<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Operation\Application\Command\MakeOperation\MakeOperationHandler;
use App\Operation\Application\Command\MakeOperation\makeOperationResponse;
use App\Operation\Infrastructure\Factories\MakeOperationCommandFactory;
use App\Operation\Infrastructure\Http\Requests\MakeOperationRequest;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Shared\Domain\Transaction\TransactionCommandHandler;
use App\Shared\Domain\Transaction\TransactionSession;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class MakeOperationAction
{

    public function __invoke(
        MakeOperationHandler $handler,
        TransactionSession   $transactionSession,
        MakeOperationRequest $request,
        OperationsLogger     $logger,
    ): JsonResponse
    {
        $httpJson = ['status' => false, 'operationSaved' => false];

        try {
            $command = MakeOperationCommandFactory::buildFromRequest($request);
            $command->previousAmount = $request->get('previousAmount') ?? 0;

            $transactionCommandHandler = new TransactionCommandHandler($handler, $transactionSession);
            $response = $transactionCommandHandler->handle($command);

            if ($response instanceof makeOperationResponse) {
                $httpJson['status'] = $response->operationSaved;
                $httpJson['operationSaved'] = $response->operationSaved;
                $httpJson['operationId'] = $response->operationId;
            }
            $httpJson['message'] = $response->message;
        } catch (InvalidArgumentException $e) {
            $logger->Log(
                message: $e->getMessage(),
                level: LogLevelEnum::CRITICAL,
                description: $e,
            );
            $httpJson['message'] = $e->getMessage();
        }

        return response()->json($httpJson);
    }
}
