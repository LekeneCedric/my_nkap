<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\Shared\Domain\Enums\ErrorMessagesEnum;
use App\User\Application\Command\VerificationAccount\VerificationAccountHandler;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Domain\Exceptions\UnknownVerificationCodeException;
use App\User\Domain\Exceptions\VerificationCodeNotMatchException;
use App\User\Infrastructure\Factories\VerificationAccountCommandFactory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class VerificationAccountAction
{
    public function __invoke(
        VerificationAccountHandler $handler,
        Request                    $request,
    ): JsonResponse
    {
        $httpResponse = [
            'status' => false,
            'message' => '',
            'accountVerified' => false
        ];

        try {
            $command = VerificationAccountCommandFactory::buildFromRequest($request);
            $response = $handler->handle($command);

            $httpResponse = [
                'status' => true,
                'message' => $response->message,
                'accountVerified' => $response->accountVerified,
            ];
        } catch (
        NotFoundUserException|
        UnknownVerificationCodeException|
        VerificationCodeNotMatchException $e) {
            $httpResponse['message'] = $e->getMessage();
        } catch (
        InvalidArgumentException|
        ErrorOnSaveUserException|
        Exception) {
            $httpResponse['message'] = ErrorMessagesEnum::TECHNICAL;
        }
        return response()->json($httpResponse);
    }
}
