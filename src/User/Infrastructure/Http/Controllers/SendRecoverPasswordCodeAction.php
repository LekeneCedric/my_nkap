<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use App\User\Application\Command\SendRecoverPasswordCode\SendRecoverPasswordCodeCommand;
use App\User\Application\Command\SendRecoverPasswordCode\SendRecoverPasswordCodeHandler;
use App\User\Domain\Enums\UserMessagesEnum;
use App\User\Domain\Exceptions\ErrorOnSaveUserException;
use App\User\Domain\Exceptions\NotFoundUserException;
use App\User\Infrastructure\Jobs\SendVerificationCodeEmail;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SendRecoverPasswordCodeAction
{
    public function __invoke(
        ChannelNotification $channelNotification,
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
            SendVerificationCodeEmail::dispatch($response->email, $response->code);
            $response->isSend = true;
            $response->message = UserMessagesEnum::RECOVER_PASSWORD_CODE_SENT;
            $httpJson = [
                'status' => true,
                'isSend' => $response->isSend,
                'message' => $response->message
            ];
            DB::commit();
        } catch (InvalidArgumentException) {
            DB::rollBack();
            $httpJson['message'] = ErrorMessagesEnum::TECHNICAL;
        } catch (NotFoundUserException $e) {
            DB::rollBack();
            $file = $e->getFile();
            $line = $e->getLine();
            $httpJson['message'] = UserMessagesEnum::NOT_FOUND;
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'AUTHENTICATION (RECOVER-PASSWORD)',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::WARNING->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => "Error in file: $file on line: $line"
                    ],
                )
            );
        } catch (ErrorOnSaveUserException|Exception $e) {
            DB::rollBack();
            $file = $e->getFile();
            $line = $e->getLine();
            $httpJson['message'] = ErrorMessagesEnum::TECHNICAL;
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'AUTHENTICATION (RECOVER-PASSWORD)',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::CRITICAL->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => "Error in file: $file on line: $line"
                    ],
                )
            );
        }
        return response()->json($httpJson);
    }
}
