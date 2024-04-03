<?php

namespace App\User\Application\Command\Login;

class LoginCommand
{
    public function __construct(
        public string $email,
        public string $password,
    ){}
}
