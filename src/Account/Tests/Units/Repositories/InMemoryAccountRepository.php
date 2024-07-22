<?php

namespace App\Account\Tests\Units\Repositories;

use App\Account\Domain\Account;
use App\Account\Domain\Repository\AccountRepository;
use App\Shared\Domain\VO\Id;

class InMemoryAccountRepository implements AccountRepository
{
    /**
     * @var Account[]
     */
    public array $account = [];

    /**
     * @param Account $account
     * @return void
     */
    public function save(Account $account): void
    {

        $this->account[$account->id()->value()] = $account;
    }

    /**
     * @param Id $accountId
     * @return Account|null
     */
    public function byId(Id $accountId): ?Account
    {
       if (array_key_exists($accountId->value(), $this->account)) {
           return $this->account[$accountId->value()];
       }
       return null;
    }
}
