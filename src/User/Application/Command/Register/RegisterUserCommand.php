<?php

namespace App\User\Application\Command\Register;

class RegisterUserCommand
{
    public function __construct(
        public string $email,
        public string $password,
        public string $username,
        public string $birthday,
        public string $professionId,
    ){}
}
