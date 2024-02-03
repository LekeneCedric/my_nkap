<?php

namespace App\Operation\Application\Command\DeleteOperation;

class DeleteOperationCommand
{
    public function __construct(
        public string $accountId,
        public string $operationId,
    )
    {
    }
}
