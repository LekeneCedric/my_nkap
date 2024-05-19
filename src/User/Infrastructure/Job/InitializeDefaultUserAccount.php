<?php

namespace App\User\Infrastructure\Job;

use App\Account\Infrastructure\Model\Account;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Uuid;

class InitializeDefaultUserAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $user_id,
        
    )
    {
    }

    public function handle(): void
    {
        try {
            $accounts = [
                [
                    'uuid' => Uuid::uuid4()->toString(),
                    'name' => 'Compte épargne',
                    'type' => 'épargne',
                    'icon' => 'balance',
                    'color' => 'green',
                    'balance' => 0.0,
                    'is_include_in_total_balance' => true,
                    'total_incomes' => 0.0,
                    'total_expenses' => 0.0,
                    'user_id' => $this->user_id
                ],
                [
                    'uuid' => Uuid::uuid4()->toString(),
                    'name' => 'Compte courant',
                    'type' => 'courant',
                    'icon' => 'balance',
                    'color' => 'green',
                    'balance' => 0.0,
                    'is_include_in_total_balance' => true,
                    'total_incomes' => 0.0,
                    'total_expenses' => 0.0,
                    'user_id' => $this->user_id
                ],
            ];
            foreach ($accounts as $account) {
                Account::create($account);
            }
        } catch (Exception $e) {
            return;
        }
    }
}
