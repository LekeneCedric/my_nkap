<?php

namespace App\Account\Infrastructure\Http\Controllers;

use App\Account\Application\Queries\All\GetAllAccountHandler;
use App\Account\Domain\Exceptions\ErrorOnGetAllAccountException;
use Exception;
use Illuminate\Http\JsonResponse;

class GetAllAccountAction
{
    public function __invoke(
        GetAllAccountHandler $handler,
        string               $userId,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'accounts' => []
        ];

        try {
             $response = $handler->handle(userId: $userId);

             $httpJson = [
                 'status' => $response->status,
                 'accounts' => $response->accounts,
             ];
        } catch (ErrorOnGetAllAccountException $e) {
            $isInDebugMode = env('APP_DEBUG');
            $httpJson['message'] = $isInDebugMode ? $e->getMessage(): 'Une erreur est survenue lors du traitement de votre requête réessayez plus-tard !';
        } catch (Exception $e) {
            $httpJson['message'] = 'Une erreur est survenue lors du traitement de votre requête réessayez plus-tard !';
        }

        return response()->json($httpJson);
    }
}
