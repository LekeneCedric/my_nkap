<?php

namespace App\User\Infrastructure\Factories;

use App\User\Application\Command\VerificationAccount\VerificationAccountCommand;
use Illuminate\Http\Request;

class VerificationAccountCommandFactory
{

    public static function buildFromRequest(Request $request): VerificationAccountCommand
    {
        return new VerificationAccountCommand(
          email: $request->get('email'),
          code: $request->get('code'),
        );
    }
}
