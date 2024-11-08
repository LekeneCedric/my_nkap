<?php

namespace App\Account\Infrastructure\Http\Controllers;

use App\Account\Application\Command\Save\SaveAccountHandler;
use App\Account\Domain\Exceptions\ErrorOnSaveAccountException;
use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Account\Infrastructure\Factories\SaveAccountCommandFactory;
use App\Account\Infrastructure\Http\Requests\SaveAccountRequest;
use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use Exception;
use Illuminate\Http\JsonResponse;

class SaveAccountAction
{
    public function __invoke(
        SaveAccountHandler $handler,
        SaveAccountRequest $request,
        ChannelNotification $channelNotification,
    ): JsonResponse
    {
        $httpJson = [
            'status' => false,
            'isSaved' => false,
        ];
        try {
            $command = SaveAccountCommandFactory::buildFromRequest($request);

            $response = $handler->handle($command);
            $httpJson['status'] = $response->status;
            $httpJson['isSaved'] = $response->isSaved;
            $httpJson['accountId'] = $response->accountId;
            $httpJson['message'] = $response->message;
        } catch (NotFoundAccountException $e){
            $file = $e->getFile();
            $line = $e->getLine();
            $httpJson['message'] = $e->getMessage();
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'Accounts',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::INFO->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => "Error in file: $file on line: $line"
                    ],
                )
            );
        } catch (ErrorOnSaveAccountException $e) {
            $file = $e->getFile();
            $line = $e->getLine();
            $httpJson['message'] = ErrorMessagesEnum::TECHNICAL;
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'Accounts',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::WARNING->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => "Error in file: $file on line: $line"
                    ],
                )
            );
        }
        catch (Exception $e) {
            $file = $e->getFile();
            $line = $e->getLine();
            $httpJson['message'] = ErrorMessagesEnum::TECHNICAL;
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'Accounts',
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
