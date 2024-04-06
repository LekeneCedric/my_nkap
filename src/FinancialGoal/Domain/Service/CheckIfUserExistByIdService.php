<?php

namespace App\FinancialGoal\Domain\Service;

use App\Shared\VO\Id;

interface CheckIfUserExistByIdService
{

    public function execute(Id $userId): bool;
}
