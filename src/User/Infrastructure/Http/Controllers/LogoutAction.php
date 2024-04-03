<?php

namespace App\User\Infrastructure\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutAction
{
    public function __invoke(): JsonResponse
    {
        try {
            auth()->user()->tokens()->delete();

            $httpResponse = [
                'status' => true,
                'isLogout' => true,
                'message' => 'Déconnexion avec succès !'
            ];
        } catch (Exception) {
            $httpResponse['message'] = 'Une érreur est survenue lors du traitement de votre opération, veuillez réssayer plus tard !';
        }

        return response()->json($httpResponse);
    }
}
