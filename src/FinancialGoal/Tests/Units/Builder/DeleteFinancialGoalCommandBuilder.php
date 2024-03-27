<?php

namespace App\FinancialGoal\Tests\Units\Builder;

use App\FinancialGoal\Application\Command\Delete\DeleteFinancialGoalCommand;

class DeleteFinancialGoalCommandBuilder
{
    private ?string $financialGoalId = null;
    public static function asCommand(): DeleteFinancialGoalCommandBuilder
    {
        return new self();
    }

    public function withFinancialGoalId(string $financialGoalId): static
    {
        $this->financialGoalId = $financialGoalId;
        return $this;
    }

    public function build(): DeleteFinancialGoalCommand
    {
        return new DeleteFinancialGoalCommand(
            financialGoalId: $this->financialGoalId,
        );
    }
}
