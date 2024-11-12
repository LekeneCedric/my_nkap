<?php

namespace App\Bootstrap\Infrastructure\Console\Commands\Dump;

use App\Account\Infrastructure\Model\Account;
use App\category\Infrastructure\Models\Category;
use App\Operation\Domain\OperationTypeEnum;
use App\Operation\Infrastructure\Model\Operation;
use App\User\Domain\Enums\UserStatusEnum;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class DumpFakeData extends Command
{

    protected $signature = 'data:dump-fake';

    protected $description = 'Dump fake data';
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            $users = User::factory(100)->create([
                'uuid' => 'fake'.Uuid::uuid4()->toString(),
                'is_active' => 1,
                'profession_id' => Profession::factory()->create()->id,
                'status' => UserStatusEnum::ACTIVE->value,
                'password' => Hash::make('password'),
            ]);

            foreach ($users as $user) {
                $accounts = Account::factory(2)->create([
                    'user_id' => $user->id,
                    'uuid' => 'fake'.Uuid::uuid4()->toString(),
                    'balance' => 1000,
                ]);
                foreach ($accounts as $account) {
                    Operation::factory(100)->create([
                        'account_id' => $account->id,
                        'uuid' => 'fake'.Uuid::uuid4()->toString(),
                        'amount' => 100,
                        'type' => OperationTypeEnum::EXPENSE->value,
                        'category_id' => Category::factory()->create([
                            'user_id' => $user->id,
                            'uuid' => 'fake'.Uuid::uuid4()->toString(),
                            'name' => 'fake',
                        ])->id,
                        'details' => 'fake',
                        'date' => now(),
                    ]);
                }
                $this->info('User with email: '.$user->email.' created');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info($e->getMessage(), 'error');
        }

    }
}
