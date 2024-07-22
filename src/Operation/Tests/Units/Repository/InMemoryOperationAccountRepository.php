<?php

namespace App\Operation\Tests\Units\Repository;

use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Shared\Domain\VO\Id;

class InMemoryOperationAccountRepository implements OperationAccountRepository
{
    /**
     * @var operationAccount[]
     */
    public array $operationsAccounts = [];
    public function byId(Id $operationAccountId): ?operationAccount
    {
        if(array_key_exists($operationAccountId->value(), $this->operationsAccounts)) {
           return $this->operationsAccounts[$operationAccountId->value()];
        }
        return null;
    }

    /**
     * @param operationAccount $operationAccount
     * @return void
     */
    public function saveOperation(operationAccount $operationAccount): void
    {
        $this->operationsAccounts[$operationAccount->id()->value()] = $operationAccount;
    }
}
