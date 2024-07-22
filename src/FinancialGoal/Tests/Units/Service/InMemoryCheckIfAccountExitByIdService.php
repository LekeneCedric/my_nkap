<?php

namespace App\FinancialGoal\Tests\Units\Service;

use App\Account\Domain\Account;
use App\FinancialGoal\Domain\Service\CheckIfAccountExitByIdService;
use App\Shared\Domain\VO\Id;

class InMemoryCheckIfAccountExitByIdService implements CheckIfAccountExitByIdService
{
    /**
     * @var Account[]
     */
    public array $accounts = [];

    public function execute(Id $accountId): bool
    {
        if (array_key_exists($accountId->value(), $this->accounts)) {
            return true;
        }
        return false;
    }
}
