<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\User\Application\Command\Login\LoginHandler;
use App\User\Domain\Enums\UserMessagesEnum;
use App\User\Infrastructure\Exceptions\NotFoundUserException;
use App\User\Infrastructure\Factories\LoginCommandFactory;
use App\User\Infrastructure\Http\Requests\LoginRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class LoginAction
{
    public function __invoke(
        LoginHandler $handler,
        LoginRequest $request,
        ChannelNotification $channelNotification,
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
        } catch (NotFoundUserException) {
            $httpResponse['message'] = UserMessagesEnum::NOT_FOUND;
        } catch (Exception $e) {
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'AUTHENTICATION (LOGIN)',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::CRITICAL->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => $e->getTraceAsString()
                    ],
                )
            );
        }
        return response()->json($httpResponse);
    }
}
