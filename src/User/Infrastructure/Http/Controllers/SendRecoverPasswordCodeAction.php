<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\User\Application\Command\SendRecoverPasswordCode\SendRecoverPasswordCodeCommand;
use App\User\Application\Command\SendRecoverPasswordCode\SendRecoverPasswordCodeHandler;
use App\User\Domain\Enums\UserMessagesEnum;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Infrastructure\Mails\EmailCodeVerificationMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendRecoverPasswordCodeAction
{
    public function __invoke(
        SendRecoverPasswordCodeHandler $handler,
        Request $request,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'isSend' => false
        ];

        try {
            DB::beginTransaction();
            $command = new SendRecoverPasswordCodeCommand(
                email: $request->get('email'),
            );
            $response = $handler->handle($command);
            Mail::to($response->email)
                ->send(new EmailCodeVerificationMail($response->code, '10 minutes'));
            $response->isSend = true;
            $response->message = UserMessagesEnum::RECOVER_PASSWORD_CODE_SENT;
            $httpJson = [
                'status' => true,
                'isSend' => $response->isSend,
                'message' => $response->message
            ];
            DB::commit();
        } catch (\InvalidArgumentException) {
            DB::rollBack();
            $httpJson['message'] = config('my-nkap.message.technical_error');
        } catch (NotFoundUserException) {
            DB::rollBack();
            $httpJson['message'] = UserMessagesEnum::NOT_FOUND;
        } catch (ErrorOnSaveUserException) {
            DB::rollBack();
            $httpJson['message'] = config('my-nkap.message.critical_technical_error');
        }
        return response()->json($httpJson);
    }
}
