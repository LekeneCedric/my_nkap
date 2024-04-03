<?php

namespace App\User\Infrastructure\Factories;

use App\User\Application\Command\Login\LoginCommand;
use Illuminate\Http\Request;
use InvalidArgumentException;

class LoginCommandFactory
{

    public static function buildFromRequest(Request $request): LoginCommand
    {
        return new LoginCommand(
            email: $request->get('email'),
            password: $request->get('password'),
        );
    }
}
