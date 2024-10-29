<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
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
        ChannelNotification $channelNotification,
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

            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::NEW_MEMBER,
                    data: [
                        'module' => 'AUTHENTICATION (RECOVER-PASSWORD)',
                        'users_data' => json_encode($response->userData, JSON_PRETTY_PRINT),
                        'total_users' => $response->countUsers,
                    ],
                )
            );
        } catch (
        NotFoundUserException|
        UnknownVerificationCodeException|
        VerificationCodeNotMatchException $e) {
            $httpResponse['message'] = $e->getMessage();
        } catch (
        InvalidArgumentException|
        ErrorOnSaveUserException|
        Exception $e) {
            $httpResponse['message'] = ErrorMessagesEnum::TECHNICAL;
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'AUTHENTICATION (VERIFICATION-ACCOUNT)',
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
