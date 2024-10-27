<?php

namespace App\Account\Infrastructure\Http\Controllers;

use App\Account\Application\Command\Delete\DeleteAccountHandler;
use App\Account\Domain\Exceptions\NotFoundAccountException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteAccountAction
{
    public function __invoke(
        DeleteAccountHandler $handler,
        Request $request,
    ): JsonResponse
    {
       $httpJson = [
         'status' => false,
         'isDeleted' => false
       ];

        try {
            $accountId = $request->get('accountId');
            $response = $handler->handle(accountToDeleteId: $accountId);
            $httpJson['accountId'] = $accountId;
            $httpJson['status'] = true;
            $httpJson['isDeleted'] = $response->isDeleted;
            $httpJson['message'] = $response->message;
        } catch (NotFoundAccountException $e) {
            $httpJson['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $httpJson['message'] = 'Une erreur est survenue lors du traitement de votre requête réessayez plus-tard !';
        }

        return response()->json($httpJson);
    }
}
