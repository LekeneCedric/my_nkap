<?php

namespace App\Account\Domain\Repository;

use App\Account\Domain\Account;
use App\Shared\VO\Id;

interface AccountRepository
{
    public function save(Account $account): void;

    public function byId(Id $accountId): ?Account;
}
