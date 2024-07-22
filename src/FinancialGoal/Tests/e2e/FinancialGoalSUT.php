<?php

namespace App\FinancialGoal\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\FinancialGoal\Infrastructure\Model\FinancialGoal;
use App\Shared\Domain\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;

class FinancialGoalSUT
{
    public ?Account $account = null;
    public ?FinancialGoal $financialGoal = null;
    public ?User $user = null;
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

    public function withExistingAccount(): static
    {
        $this->account = Account::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 0
        ]);
        return $this;
    }

    public function withExistingFinancialGoal(int $count = 1): static
    {
        for($i = 0; $i<$count; $i++){
            $this->financialGoal = FinancialGoal::factory()->create([
                'user_id' => $this->user->id,
                'account_id' => $this->account->id,
            ]);
        }
        return $this;
    }

    public function build(): static
    {
        return $this;
    }
}
