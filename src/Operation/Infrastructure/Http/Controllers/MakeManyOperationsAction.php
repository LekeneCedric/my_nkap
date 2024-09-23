<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Operation\Application\Command\MakeManyOperations\MakeManyOperationResponse;
use App\Operation\Application\Command\MakeManyOperations\MakeManyOperationsHandler;
use App\Operation\Infrastructure\Factories\MakeManyOperationsCommandFactory;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Shared\Domain\Transaction\TransactionCommandHandler;
use App\Shared\Domain\Transaction\TransactionSession;
use App\Shared\Infrastructure\Logs\Enum\LogLevelEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MakeManyOperationsAction
{
    public function __invoke(
        MakeManyOperationsHandler $handler,
        TransactionSession $transactionSession,
        Request $request,
        OperationsLogger $logger,
    ): JsonResponse
    {
        $httpJson = ['status' => false, 'operationsSaved' => false];

        try {
            $command = MakeManyOperationsCommandFactory::buildFromRequest($request);

            $transactionCommandHandler = new TransactionCommandHandler($handler, $transactionSession);
            $response = $transactionCommandHandler->handle($command);

            if ($response instanceof MakeManyOperationResponse) {
                $httpJson['status'] = $response->operationsSaved;
                $httpJson['operationsSaved'] = $response->operationsSaved;
                $httpJson['operationIds'] = $response->operationIds;
            }
            $httpJson['message'] = $response->message;
        } catch (\InvalidArgumentException $e) {
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
