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
    public static function asSUT(): AccountSUT
    {
        $self = new self();
        $self->accounts = [];
        return $self;
    }

    public function withExistingAccounts(int $count): static
    {
        for($i=0; $i<$count; $i++){
            $this->accounts[] = Account::factory()->create();
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
