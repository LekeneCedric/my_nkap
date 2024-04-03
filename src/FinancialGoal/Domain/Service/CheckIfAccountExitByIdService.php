<?php

namespace App\FinancialGoal\Domain\Service;

use App\Shared\VO\Id;

interface CheckIfAccountExitByIdService
{

    public function execute(Id $accountId): bool;
}
