<?php

namespace App\User\Application\Command\SendRecoverPasswordCode;

class SendRecoverPasswordCodeResponse
{
    public bool $isSend = false;
    public string $message = '';
    public string $email = '';
    public string $code = '';
}
