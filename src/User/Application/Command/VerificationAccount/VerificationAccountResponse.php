<?php

namespace App\User\Application\Command\VerificationAccount;

class VerificationAccountResponse
{
    public string $message = '';
    public bool $accountVerified = false;
    public array $userData = [];
    public array $subscriptionData = [];
    public int $countUsers = 0;
}
