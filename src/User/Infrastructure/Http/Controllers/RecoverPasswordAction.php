<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\User\Application\Command\RecoverPassword\RecoverPasswordHandler;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Domain\Exceptions\UnknownVerificationCodeException;
use App\User\Domain\Exceptions\VerificationCodeNotMatchException;
use App\User\Infrastructure\Factories\RecoverPasswordCommandFactory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class RecoverPasswordAction
{
    public function __invoke(
        RecoverPasswordHandler $handler,
        Request $request,
    ): JsonResponse
    {
        $httpResponse = [
            'status' => false,
            'passwordReset' => false,
        ];

        try {
            $command = RecoverPasswordCommandFactory::buildFromRequest($request);

            $response = $handler->handle($command);

            $httpResponse = [
              'status' => true,
              'passwordReset' => $response->passwordReset
            ];
        } catch (
            InvalidArgumentException|
            NotFoundUserException|
            UnknownVerificationCodeException|
            VerificationCodeNotMatchException $e) {
            $httpResponse['message'] = $e->getMessage();
        } catch (ErrorOnSaveUserException $e) {
            $httpResponse['message'] = $e->getMessage();
        } catch (Exception) {
            $httpResponse['message'] = config('my-nkap.message.critical_technical_error');
        }

        return response()->json($httpResponse);
    }
}