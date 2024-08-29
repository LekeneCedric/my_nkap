<?php

namespace App\Shared\Infrastructure\Transaction;

use App\Shared\Domain\Transaction\TransactionExecutionErrorException;
use App\Shared\Domain\Transaction\TransactionSession;
use Illuminate\Support\Facades\DB;

class EloquentTransactionSession implements TransactionSession
{
    public function executeAtomically(callable $operation): mixed
    {
        try {
            return DB::transaction($operation);
        } catch (\Throwable $e) {
            throw new TransactionExecutionErrorException(
              $e->getMessage(),
                (int)$e->getCode(),
                $e->getFile(),
                $e->getLine(),
                $e->getTrace(),
                $e
            );
        }
    }
}
