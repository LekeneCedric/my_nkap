<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\User\Application\Command\Register\RegisterUserHandler;
use App\User\Domain\Exceptions\AlreadyUserExistWithSameEmailException;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Infrastructure\Factories\RegisterUserCommandFactory;
use App\User\Infrastructure\Http\Requests\RegisterUserRequest;
use App\User\Infrastructure\Job\InitializeDefaultUserAccount;
use App\User\Infrastructure\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RegisterAction
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
            DB::beginTransaction();
            $command = RegisterUserCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);
            $user = User::where('uuid', $response->userId)->first(['id']);
            $httpResponse = [
                'status' => true,
                'isCreated' => $response->isCreated,
                'message' => $response->message,
                'token' => $user?->createToken(env('TOKEN_KEY'))->plainTextToken,
                'user' => $response->user,
            ];
            DB::commit();
        } catch (ErrorOnSaveUserException) {
            DB::rollBack();
            $httpResponse['message'] = 'Une érreur technique est survenue lors du traitement de votre opération !';
        } catch (AlreadyUserExistWithSameEmailException $e) {
            DB::rollBack();
            $httpResponse['message'] = $e->getMessage();
        }

        return response()->json($httpResponse);
    }
}
