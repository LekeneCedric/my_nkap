<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\Shared\Domain\Enums\ErrorMessagesEnum;
use App\User\Domain\Enums\UserMessagesEnum;
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
                'message' => UserMessagesEnum::LOGOUT
            ];
        } catch (Exception) {
            $httpResponse['message'] = ErrorMessagesEnum::TECHNICAL;
        }

        return response()->json($httpResponse);
    }
}
