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
    public string $subscriptionId = '';
    public int $subscriptionStartDate = 0;
    public int $subscriptionEndDate = 0;
    public int $nbTokenUpdatedAt = 0;
    public int $nbOperationsUpdatedAt = 0;
    public int $nbTokenPerDay = 0;
    public int $nbOperationsPerDay = 0;
}
