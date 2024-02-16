<?php

namespace App\Operation\Infrastructure\Http\Controllers;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Application\Command\MakeOperation\MakeOperationHandler;
use App\Operation\Domain\Exceptions\OperationGreaterThanAccountBalanceException;
use App\Operation\Infrastructure\Http\Factories\MakeOperationCommandFactory;
use App\Operation\Infrastructure\Http\Requests\MakeOperationRequest;
use Illuminate\Http\JsonResponse;

class MakeOperationAction
{
    public function __invoke(
        MakeOperationHandler $handler,
        MakeOperationRequest $request,
    ): JsonResponse
    {
        $httpJson = ['status' => false, 'operationIsSaved' => false];

        try {
            $command = MakeOperationCommandFactory::buildFromRequest($request);
            $reponse = $handler->handle($command);

            $httpJson = [
                'status' => true,
                'operationSaved' => $reponse->operationSaved,
            ];
        } catch (
            NotFoundAccountException|
            OperationGreaterThanAccountBalanceException $e
        ) {
            $httpJson['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $httpJson['message'] = 'Une erreur est survenue lors du traitement de votre requête , veillez réessayer ultérieurement !';
        }

        return response()->json($httpJson);
    }
}
