<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Operation\Application\Command\DeleteOperation\DeleteOperationHandler;
use App\Operation\Application\Command\DeleteOperation\DeleteOperationResponse;
use App\Operation\Infrastructure\Factories\DeleteOperationCommandFactory;
use App\Operation\Infrastructure\Http\Requests\DeleteOperationRequest;
use App\Operation\Infrastructure\Logs\OperationsLogger;
use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Transaction\TransactionCommandHandler;
use App\Shared\Domain\Transaction\TransactionSession;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class DeleteOperationAction
{
    public function __invoke(
        ChannelNotification $channelNotification,
        DeleteOperationHandler $handler,
        TransactionSession     $transactionSession,
        DeleteOperationRequest $request,
        OperationsLogger       $logger,
    ): JsonResponse
    {
        $httpJson = ['status' => false];

        try {
            $command = DeleteOperationCommandFactory::buildFromRequest($request);
            $transactionCommandHandler = new TransactionCommandHandler($handler, $transactionSession);
            $response = $transactionCommandHandler->handle($command);

            if ($response instanceof DeleteOperationResponse) {
                $httpJson['status'] = $response->isDeleted;
                $httpJson['isDeleted'] = $response->isDeleted;
                $httpJson['operationId'] = $command->operationId;
            }
            $httpJson['message'] = $response->message;
        } catch (InvalidArgumentException $e) {
            $httpJson['message'] = $e->getMessage();
        }

        return response()->json($httpJson);
    }
}
