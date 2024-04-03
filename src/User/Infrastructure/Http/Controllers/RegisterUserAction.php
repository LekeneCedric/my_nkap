<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\User\Application\Command\RegisterUserHandler;
use App\User\Domain\Exceptions\AlreadyUserExistWithSameEmailException;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Infrastructure\Factories\RegisterUserCommandFactory;
use App\User\Infrastructure\Http\Requests\RegisterUserRequest;
use App\User\Infrastructure\Models\User;
use Illuminate\Http\JsonResponse;

class RegisterUserAction
{
    public function __invoke(
        RegisterUserHandler $handler,
        RegisterUserRequest $request,
    ): JsonResponse
    {
        $httpResponse = [
            'status' => false,
        ];
        try {
            $command = RegisterUserCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);
            $user = User::where('uuid', $response->userId)->first();
            $httpResponse = [
                'status' => true,
                'isCreated' => $response->isCreated,
                'message' => $response->message,
                'token' => $user?->createToken('my_nkap_token')->plainTextToken,
            ];
        } catch (ErrorOnSaveUserException $e) {
            $httpResponse['message'] = 'Une érreur technique est survenue lors du traitement de votre opération !';
        } catch (AlreadyUserExistWithSameEmailException $e) {
            $httpResponse['message'] = $e->getMessage();
        }


        return response()->json($httpResponse);
    }
}
