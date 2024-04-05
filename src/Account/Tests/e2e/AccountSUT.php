<?php

namespace App\Account\Tests\e2e;

use App\Account\Infrastructure\Model\Account;
use App\Operation\Infrastructure\Model\Operation;
use App\Shared\VO\Id;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;

class AccountSUT
{
    /**
     * @var Account[]
     */
    public array $accounts;
    public User $user;
    public static function asSUT(): AccountSUT
    {
        $self = new self();
        $self->accounts = [];
        $self->user = User::factory()->create([
            'uuid' => (new Id())->value(),
            'email' => (new Id())->value().'@gmail.com',
            'name' => 'leke',
            'password' => bcrypt('leke@5144'),
            'profession_id' => (Profession::factory()->create())->id,
        ]);
        return $self;
    }

    public function withExistingAccounts(int $count, ?int $userId = null): static
    {
        for($i=0; $i<$count; $i++){
            $this->accounts[] = Account::factory()->create([
                'user_id' => $userId ?: $this->user->id
            ]);
        }

        return $this;
    }

    public function withExistingOperationsPerAccounts(int $count): static
    {
        foreach ($this->accounts as $account) {
            for($i=0; $i<$count; $i++) {
                Operation::factory()->create([
                    'account_id' => $account->getAttributeValue('id')
                ]);
            }
        }

        return $this;
    }

    public function build(): static
    {
        return $this;
    }
}
