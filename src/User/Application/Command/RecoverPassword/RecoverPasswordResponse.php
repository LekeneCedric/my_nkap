<?php

namespace App\User\Application\Command\RecoverPassword;

class RecoverPasswordResponse
{
    public bool $passwordReset = false;
    public string $message = '';
}
