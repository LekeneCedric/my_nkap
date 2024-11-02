<?php

namespace App\User\Application\Command\Login;

use App\Shared\Domain\VO\DateVO;
use App\User\Domain\Enums\UserTokenEnum;
use App\User\Infrastructure\Exceptions\NotFoundUserException;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginHandler
{

    /**
     * @param LoginCommand $command
     * @return LoginResponse
     * @throws NotFoundUserException
     */
    public function handle(LoginCommand $command): LoginResponse
    {
        $response = new LoginResponse();

        $user = User::where('email', $command->email)->where('is_active', true)->where('is_deleted', false)->first();

        if (!$user || !Hash::check($command->password, $user->password)) {
            throw new NotFoundUserException();
        }

        $userData = [
          'userId' => $user->uuid,
          'email' => $user->email,
          'name' => $user->name,
          'profession' => Profession::where('id', $user->profession_id)->first()->name,
        ];
        $aiToken = $user->token;
        $aiTokenUpdatedAt = new DateVO($user->token_updated_at);
        if ($aiTokenUpdatedAt->isFromPreviousDay()) {
            $aiToken = UserTokenEnum::DEFAULT_TOKEN;
            User::whereUuid($user->uuid)
                ->update([
                    'token' => $aiToken,
                    'token_updated_at' => $aiTokenUpdatedAt->formatYMDHIS(),
                ]);
        }
        $token = $user->createToken(env('TOKEN_KEY'))->plainTextToken;

        $response->isLogged = true;
        $response->user  = $userData;
        $response->token = $token;
        $response->aiToken = $aiToken;
        return $response;
    }
}
