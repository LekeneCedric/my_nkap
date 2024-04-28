<?php

namespace App\Profession\Infrastructure\Http\Controllers;

use App\Profession\Application\Queries\GetAll\GetAllProfessionQueryHandler;
use Illuminate\Http\JsonResponse;

class GetAllProfessionAction
{
    public function __invoke(
        GetAllProfessionQueryHandler $handler,
    ): JsonResponse
    {
        $httpResponse = [
            'status' => false,
        ];

        try {
            $response = $handler->handle();
            $httpResponse = [
                'status' => true,
                'professions' => $response->professions,
            ];
        } catch (\Exception $e) {
            $httpResponse['message'] = 'Une erreur est survenue !';
        }

        return response()->json($httpResponse);
    }
}
