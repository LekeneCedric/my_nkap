<?php

namespace App\User\Application\Command\Login;

use App\User\Infrastructure\Exceptions\NotFoundUserException;
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
            throw new NotFoundUserException("Information de connexion incorrect : Aucun compte correspondant !");
        }

        $userData = [
          'email' => $user->email,
          'name' => $user->name,
        ];
        $token = $user->createToken('my_nkap_token')->plainTextToken;

        $response->isLogged = true;
        $response->user  = $userData;
        $response->token = $token;
        return $response;
    }
}
