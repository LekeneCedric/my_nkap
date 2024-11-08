<?php

namespace App\User\Application\Command\Login;

class LoginResponse
{
    public bool $isLogged = false;
    public array $user = [];
    public string $token = '';
    public int $leftNbToken = 0;
    public int $leftNbOperations = 0;
    public int $leftNbAccounts = 0;
}
