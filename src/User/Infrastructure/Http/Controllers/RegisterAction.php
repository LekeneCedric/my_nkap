<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\User\Application\Command\Register\RegisterUserHandler;
use App\User\Domain\Exceptions\AlreadyUserExistWithSameEmailException;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Infrastructure\Factories\RegisterUserCommandFactory;
use App\User\Infrastructure\Http\Requests\RegisterUserRequest;
use App\User\Infrastructure\Jobs\SendVerificationCodeEmail;
use App\User\Infrastructure\Models\User;
use Exception;
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
        DB::beginTransaction();
        try {
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
            SendVerificationCodeEmail::dispatch($command->email, $response->code);
        } catch (AlreadyUserExistWithSameEmailException $e) {
            DB::rollBack();
            $httpResponse['message'] = $e->getMessage();
        }catch (ErrorOnSaveUserException|Exception) {
            DB::rollBack();
            $httpResponse['message'] = config('my-nkap.message.critical_technical_error');
        }

        return response()->json($httpResponse);
    }
}
