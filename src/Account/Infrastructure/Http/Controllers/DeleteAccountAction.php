<?php

namespace App\Account\Infrastructure\Http\Controllers;

use App\Account\Application\Command\Delete\DeleteAccountHandler;
use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteAccountAction
{
    public function __invoke(
        DeleteAccountHandler $handler,
        Request $request,
        ChannelNotification $channelNotification,
    ): JsonResponse
    {
       $httpJson = [
         'status' => false,
         'isDeleted' => false
       ];

        try {
            $accountId = $request->get('accountId');
            $response = $handler->handle(accountToDeleteId: $accountId);
            $httpJson['accountId'] = $accountId;
            $httpJson['status'] = true;
            $httpJson['isDeleted'] = $response->isDeleted;
            $httpJson['message'] = $response->message;
        } catch (NotFoundAccountException $e) {
            $httpJson['message'] = $e->getMessage();
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'Accounts',
                        'level' => ErrorLevelEnum::INFO->value,
                        'message' => $e->getMessage(),
                        'command' => json_encode(['accountId' => $accountId], JSON_PRETTY_PRINT),
                        'trace' => $e->getTraceAsString()
                    ],
                )
            );
        } catch (Exception $e) {
            $httpJson['message'] = ErrorMessagesEnum::TECHNICAL;
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'Accounts',
                        'level' => ErrorLevelEnum::CRITICAL->value,
                        'command' => json_encode(['accountId' => $accountId], JSON_PRETTY_PRINT),
                        'trace' => $e->getTraceAsString()
                    ],
                )
            );
        }

        return response()->json($httpJson);
    }
}
