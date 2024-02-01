<?php

namespace App\Account\Infrastructure\Http\Controllers;

use App\Account\Application\Command\Save\SaveAccountHandler;
use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Account\Infrastructure\Http\Factories\SaveAccountCommandFactory;
use App\Account\Infrastructure\Http\Requests\SaveAccountRequest;
use Illuminate\Http\JsonResponse;

class SaveAccountAction
{
    public function __invoke(
        SaveAccountHandler $handler,
        SaveAccountRequest $request,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'isSaved' => false,
        ];
        try {
            $command = SaveAccountCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);
            $httpJson['status'] = $response->status;
            $httpJson['isSaved'] = $response->isSaved;
            $httpJson['accountId'] = $response->accountId;
            $httpJson['message'] = $response->message;
        } catch (NotFoundAccountException $e){
            $httpJson['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $httpJson['message'] = 'Une erreur est survenue lors du traitement de votre requête , veillez réessayer ultérieurement !';
        }

        return response()->json($httpJson);
    }
}
