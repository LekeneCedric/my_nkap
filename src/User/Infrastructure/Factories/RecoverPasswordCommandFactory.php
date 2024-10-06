<?php

namespace App\User\Infrastructure\Factories;

use App\User\Application\Command\RecoverPassword\RecoverPasswordCommand;
use Illuminate\Http\Request;
use InvalidArgumentException;

class RecoverPasswordCommandFactory
{

    public static function buildFromRequest(Request $request): RecoverPasswordCommand
    {
        self::validate($request);
        return new RecoverPasswordCommand(
            code: $request->get('code'),
            email: $request->get('email'),
            password: $request->get('password'),
        );
    }

    private static function validate(Request $request): void
    {
        if (!$request->get('code') || !$request->get('email') || !$request->get('password')) {
            throw new InvalidArgumentException('error in commands');
        }
    }
}
