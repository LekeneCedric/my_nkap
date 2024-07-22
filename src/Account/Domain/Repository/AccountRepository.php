<?php

namespace App\Account\Domain\Repository;

use App\Account\Domain\Account;
use App\Account\Domain\Exceptions\ErrorOnSaveAccountException;
use App\Shared\Domain\VO\Id;

interface AccountRepository
{
    /**
     * @param Account $account
     * @return void
     * @throws ErrorOnSaveAccountException
     */
    public function save(Account $account): void;

    public function byId(Id $accountId): ?Account;
}
