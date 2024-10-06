<?php

namespace App\User\Application\Command\VerificationAccount;

class VerificationAccountCommand
{
    public function __construct(
        public string $email,
        public string $code,
    )
    {
    }
}
