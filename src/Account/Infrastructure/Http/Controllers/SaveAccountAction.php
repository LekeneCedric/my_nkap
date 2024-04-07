<?php

namespace App\Account\Infrastructure\Http\Controllers;

use App\Account\Application\Command\Save\SaveAccountHandler;
use App\Account\Domain\Exceptions\ErrorOnSaveAccountException;
use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Account\Infrastructure\Factories\SaveAccountCommandFactory;
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
            dd($e);
            $httpJson['message'] = $e->getMessage();
        } catch (ErrorOnSaveAccountException) {
            dd($e);
            $httpJson['message'] = 'Une érreur critique est survenue lors du traitement de votre opération , veuillez réessayez plus târd !';
        }
        catch (\Exception $e) {
            dd($e);
            $httpJson['message'] = 'Une erreur est survenue lors du traitement de votre requête , veuillez réessayer ultérieurement !';
        }

        return response()->json($httpJson);
    }
}
