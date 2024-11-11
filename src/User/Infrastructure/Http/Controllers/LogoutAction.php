<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use App\User\Domain\Enums\UserMessagesEnum;
use Exception;
use Illuminate\Http\JsonResponse;

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
        } catch (Exception $e) {
            dd($e);
            $httpResponse['message'] = ErrorMessagesEnum::TECHNICAL;
        }

        return response()->json($httpResponse);
    }
}
