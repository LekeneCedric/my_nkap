<?php

namespace App\User\Infrastructure\Observers;

use App\Account\Infrastructure\Model\Account;
use App\User\Infrastructure\Models\User;
use Ramsey\Uuid\Uuid;

class UserObserver
{
    public function created(User $user): void
    {
        $accounts = [
            [
                'uuid' => Uuid::uuid4()->toString(),
                'name' => 'Compte épargne',
                'type' => 'épargne',
                'icon' => 'wallet',
                'color' => 'green',
                'balance' => 0.0,
                'is_include_in_total_balance' => true,
                'total_incomes' => 0.0,
                'total_expenses' => 0.0,
                'user_id' => $user->id
            ],
            [
                'uuid' => Uuid::uuid4()->toString(),
                'name' => 'Compte courant',
                'type' => 'courant',
                'icon' => 'credit-card',
                'color' => 'green',
                'balance' => 0.0,
                'is_include_in_total_balance' => true,
                'total_incomes' => 0.0,
                'total_expenses' => 0.0,
                'user_id' => $user->id
            ],
        ];
        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
