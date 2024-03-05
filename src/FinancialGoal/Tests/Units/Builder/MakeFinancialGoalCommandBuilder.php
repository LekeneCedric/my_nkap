<?php

namespace App\FinancialGoal\Tests\Units\Builder;

use App\FinancialGoal\Application\Command\MakeFinancialGoalCommand;

class MakeFinancialGoalCommandBuilder
{
    private ?string $accountId = null;
    private ?string $startDate = null;
    private ?string $endDate = null;
    private ?float $desiredAmount = null;
    private ?string $details = null;
    private ?string $financialGoalId = null;

    public static function asCommand()
    {
        return new self();
    }

    public function withAccountId(string $accountId): static
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function withStartDate(string $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function withEndDate(string $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function withDesiredAmount(float $amount): static
    {
        $this->desiredAmount = $amount;
        return $this;
    }

    public function withDetail(string $details): static
    {
        $this->details = $details;
        return $this;
    }

    public function withFinancialGoalId(string $financialGoalId): static
    {
        $this->financialGoalId = $financialGoalId;
        return $this;
    }

    public function build(): MakeFinancialGoalCommand
    {
        $self = new MakeFinancialGoalCommand(
            accountId: $this->accountId,
            startDate: $this->startDate,
            endDate: $this->endDate,
            desiredAmount: $this->desiredAmount,
            details: $this->details,
        );
        $self->financialGoalId = $this->financialGoalId;
        return $self;
    }
}
