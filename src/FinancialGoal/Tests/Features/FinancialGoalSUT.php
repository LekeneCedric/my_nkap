<?php

namespace App\FinancialGoal\Tests\Features;

use App\Account\Infrastructure\Model\Account;
use App\FinancialGoal\Domain\FinancialGoal;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;

class FinancialGoalSUT
{
    public FinancialGoal $financialGoal;
    private User $user;
    public static function asSUT(): FinancialGoalSUT
    {
        $self = new self();
        $self->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value().'@gmail.com',
            'name' => 'lekene',
            'password' => bcrypt('lekene@5144'),
            'profession_id' => (Profession::factory()->create())->id,
        ]);
        return $self;
    }

    public function withFinancialGoal(): static
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $this->financialGoal = FinancialGoal::create(
            userId: new Id($this->user->uuid),
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
