<?php

namespace App\FinancialGoal\Tests\Features;

use App\Account\Infrastructure\Model\Account;
use App\FinancialGoal\Domain\FinancialGoal;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;

class FinancialGoalSUT
{
    public FinancialGoal $financialGoal;
    public static function asSUT(): FinancialGoalSUT
    {
        return new self();
    }

    public function withFinancialGoal(): static
    {
        $account = Account::factory()->create();
        $this->financialGoal = FinancialGoal::create(
            accountId: new Id($account->uuid),
            startDate: new DateVO(),
            enDate: new DateVO('2022-09-30 10:00:00'),
            desiredAmount: new AmountVO(200000),
            details: new StringVO('I need to save 200 000 FCFA before december')
        );

        return $this;
    }

    public function build(): static
    {
        return $this;
    }
}
