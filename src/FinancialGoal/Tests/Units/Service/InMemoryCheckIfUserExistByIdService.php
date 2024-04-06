<?php

namespace App\FinancialGoal\Tests\Units\Service;

use App\FinancialGoal\Domain\Service\CheckIfUserExistByIdService;
use App\FinancialGoal\FinancialGoalUser;
use App\Shared\VO\Id;

class InMemoryCheckIfUserExistByIdService implements CheckIfUserExistByIdService
{
    /**
     * @var FinancialGoalUser[]
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
