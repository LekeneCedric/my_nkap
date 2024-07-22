<?php

namespace App\FinancialGoal\Domain\Service;

use App\Shared\Domain\VO\Id;

interface CheckIfUserExistByIdService
{

    public function execute(Id $userId): bool;
}
