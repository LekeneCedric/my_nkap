<?php

namespace App\Operation\Domain;

use App\Shared\Domain\VO\Id;

interface OperationAccountRepository
{
    /**
     * @param Id $operationAccountId
     * @return operationAccount|null
     */
    public function byId(Id $operationAccountId): ?operationAccount;

    /**
     * @param operationAccount $operationAccount
     * @return void
     */
    public function saveOperation(operationAccount $operationAccount): void;
}
