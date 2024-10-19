<?php

namespace App\User\Application\Command\Login;

class LoginResponse
{
    public bool $isLogged = false;
    public array $user = [];
    public string $token = '';
    public int $aiToken = 0;
}
