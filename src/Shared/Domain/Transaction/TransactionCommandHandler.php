<?php

namespace App\Shared\Domain\Transaction;

use App\Shared\Domain\Command\Command;
use App\Shared\Domain\Command\CommandHandler;

class TransactionCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly CommandHandler $handler,
        private readonly TransactionSession      $session,
    )
    {
    }

    public function handle(Command $command): mixed
    {
        $operation = function() use ($command) {
          return $this->handler->handle($command);
        };

        try {
            return $this->session->executeAtomically($operation);
        } catch (TransactionExecutionErrorException $e) {
            return new TransactionalResponse();
        }
    }
}
