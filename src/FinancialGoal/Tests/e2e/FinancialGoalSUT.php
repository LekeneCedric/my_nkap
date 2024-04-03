<?php

namespace App\FinancialGoal\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\FinancialGoal\Infrastructure\Model\FinancialGoal;

class FinancialGoalSUT
{
    public ?Account $account = null;
    public ?FinancialGoal $financialGoal = null;
    public static function asSUT(): FinancialGoalSUT
    {
        return new self();
    }

    public function withExistingAccount(): static
    {
        $this->account = Account::factory()->create([
            'balance' => 0
        ]);
        return $this;
    }

    public function withExistingFinancialGoal(): static
    {
        $this->financialGoal = FinancialGoal::factory()->create([
           'account_id' => $this->account->id,
        ]);
        return $this;
    }

    public function build(): static
    {
        return $this;
    }
}
