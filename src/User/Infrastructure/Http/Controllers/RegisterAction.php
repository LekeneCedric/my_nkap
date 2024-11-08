<?php

namespace App\User\Infrastructure\Http\Controllers;

use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\User\Application\Command\Register\RegisterUserHandler;
use App\User\Domain\Exceptions\AlreadyUserExistWithSameEmailException;
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
        ChannelNotification $channelNotification,
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
        } catch (Exception $e) {
            DB::rollBack();
            $file = $e->getFile();
            $line = $e->getLine();
            $httpResponse['message'] = $command->professionId;
            $channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'AUTHENTICATION (REGISTER)',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::CRITICAL->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => "Error in file: $file on line: $line"
                    ],
                )
            );
        }

        return response()->json($httpResponse);
    }
}
