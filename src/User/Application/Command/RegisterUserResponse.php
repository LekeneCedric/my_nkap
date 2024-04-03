<?php

namespace App\User\Application\Command;

class RegisterUserResponse
{
        public bool   $isCreated = false;
        public string $message = '';
        public string $userId = '';
}
