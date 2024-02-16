<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Application\Command\DeleteOperation\DeleteOperationHandler;
use App\Operation\Domain\Exceptions\NotFoundOperationException;
use App\Operation\Infrastructure\Http\Factories\DeleteOperationCommandFactory;
use App\Operation\Infrastructure\Http\Requests\DeleteOperationRequest;
use Illuminate\Http\JsonResponse;

class DeleteOperationAction
{
    public function __invoke(
        DeleteOperationHandler $handler,
        DeleteOperationRequest $request,
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
            $httpJson['message'] = $e->getMessage();
        } catch (\Exception) {
            $httpJson['message'] = 'Une erreur est survenue lors du traitement de votre requête , veillez réessayer ultérieurement !';
        }

        return response()->json($httpJson);
    }
}
