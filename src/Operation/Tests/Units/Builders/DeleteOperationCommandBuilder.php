<?php

namespace App\Operation\Tests\Units\Builders;

use App\Operation\Application\Command\DeleteOperation\DeleteOperationCommand;

class DeleteOperationCommandBuilder
{
    private ?string $accountId = null;
    private ?string $operationId = null;
    public static function asCommand(): DeleteOperationCommandBuilder
    {
        return new self();
    }

    public function withAccountId(string $accountId): static
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function withOperationId(string $operationId): static
    {
        $this->operationId = $operationId;
        return $this;
    }

    public function build()
    {
        return new DeleteOperationCommand(
          accountId: $this->accountId,
          operationId: $this->operationId,
        );
    }
}
