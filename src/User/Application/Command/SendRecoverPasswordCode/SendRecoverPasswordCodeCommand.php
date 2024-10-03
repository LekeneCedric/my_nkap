<?php

namespace App\User\Application\Command\SendRecoverPasswordCode;

class SendRecoverPasswordCodeCommand
{
    public function __construct(
        public string $email,
    )
    {
    }
}
