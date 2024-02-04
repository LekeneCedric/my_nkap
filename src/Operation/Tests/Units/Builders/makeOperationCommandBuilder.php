<?php

namespace App\Operation\Tests\Units\Builders;

use App\Operation\Application\Command\MakeOperation\makeOperationCommand;
use App\Operation\Domain\OperationTypeEnum;

class makeOperationCommandBuilder
{
    private string $accountId;
    private ?string $operationId = null;
    private OperationTypeEnum $type;
    private string $category;
    private string $detail;
    private float $amount;
    private string $date;

    public static function asCommand(): makeOperationCommandBuilder
    {
        return new self();
    }

    public function withAccountId(string $accountId): static
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function withOperationId(mixed $operationId): static
    {
        $this->operationId = $operationId;
        return $this;
    }

    public function withType(OperationTypeEnum $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function withAmount(int $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function withCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function withDetail(string $detail): static
    {
        $this->detail = $detail;
        return $this;
    }

    public function withDate(string $operationDate): static
    {
        $this->date = $operationDate;
        return $this;
    }

    public function build(): makeOperationCommand
    {
        $command =  new makeOperationCommand(
            accountId: $this->accountId,
            type: $this->type,
            amount: $this->amount,
            category: $this->category,
            detail: $this->detail,
            date: $this->date,
        );
        $command->operationId = $this->operationId;

        return $command;
    }

}
