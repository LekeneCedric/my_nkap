<?php

namespace App\FinancialGoal\Domain\Service;

use App\Shared\Domain\VO\Id;

interface CheckIfAccountExitByIdService
{

    public function execute(Id $accountId): bool;
}
