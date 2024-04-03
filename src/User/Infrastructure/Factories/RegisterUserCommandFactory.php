<?php

namespace App\User\Infrastructure\Factories;

use App\User\Application\Command\RegisterUserCommand;
use Illuminate\Http\Request;

class RegisterUserCommandFactory
{

    public static function buildFromRequest(Request $request): RegisterUserCommand
    {
        return new RegisterUserCommand(
            email: $request->get('email'),
            password: $request->get('password'),
            username: $request->get('username'),
            birthday: $request->get('birthday'),
            professionId: $request->get('professionId'),
        );
    }
}
