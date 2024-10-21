<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\User\Application\Command\Login\LoginHandler;
use App\User\Infrastructure\Exceptions\NotFoundUserException;
use App\User\Infrastructure\Factories\LoginCommandFactory;
use App\User\Infrastructure\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;

class LoginAction
{
    public function __invoke(
        LoginHandler $handler,
        LoginRequest $request,
    ): JsonResponse
    {
        $httpResponse = ['status' => false, 'isLogged' => false];

        try {
            $command = LoginCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);
            $httpResponse = [
                'status' => true,
                'isLogged' => $response->isLogged,
                'user' => $response->user,
                'token' => $response->token,
                'message' => $response->user['name'],
                'aiToken' => $response->aiToken,
            ];
        } catch (NotFoundUserException $e) {
            $httpResponse['message'] = 'not_found_user';
        } catch (\Exception $e) {
            $httpResponse['message'] = $e->getMessage();
        }
        return response()->json($httpResponse);
    }
}
