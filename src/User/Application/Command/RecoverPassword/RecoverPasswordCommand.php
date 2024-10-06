<?php

namespace App\User\Application\Command\RecoverPassword;

class RecoverPasswordCommand
{
    public function __construct(
        public string $code,
        public string $email,
        public string $password,
    )
    {
    }
}
