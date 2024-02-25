<?php

namespace App\Account\Tests\e2e;

use App\Account\Infrastructure\Models\Account;
use App\Operation\Infrastructure\Model\Operation;

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
