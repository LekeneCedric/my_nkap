<?php

namespace App\FinancialGoal\Tests\Units\Service;

use App\FinancialGoal\Domain\Service\CheckIfUserExistByIdService;
use App\Shared\Domain\VO\Id;

class InMemoryCheckIfUserExistByIdService implements CheckIfUserExistByIdService
{
    /**
     * @var Id[]
     */
    public array $users = [];

    public function execute(Id $userId): bool
    {
        if (array_key_exists($userId->value(), $this->users)) {
            return true;
        }
        return false;
    }
}
