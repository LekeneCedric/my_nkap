<?php

namespace App\Shared\Domain\Transaction;

interface TransactionSession
{
    /**
     * @param callable $operation
     * @return mixed
     * @throws TransactionExecutionErrorException
     */
    public function executeAtomically(callable $operation): mixed;
}
