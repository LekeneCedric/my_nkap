<?php

namespace App\Bootstrap\Infrastructure\Console\Commands\Dump;

use App\Subscription\Domain\Enums\SubscriptionPlansEnum;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class DumpSubscriptionsCommand extends Command
{
    protected $description = 'Dump default subscriptions';

    protected $signature = 'data:dump-subscriptions';

    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $subscriptions = $this->getSubscriptions();
            DB::table('subscriptions')->insert($subscriptions);
            DB::commit();
            $this->info('[+] Subscriptions successfully dumped', 'success');
        } catch (Exception $e) {
            DB::rollBack();
            $this->info($e->getMessage(), 'error');
        }
    }
    private function getSubscriptions(): array
    {
        return [
            [
                'uuid' => Uuid::uuid4()->toString(),
                'name' => SubscriptionPlansEnum::FREE_PLAN->value,
                'price' => 0.0,
                'nb_token_per_day' => 5000,
                'nb_operations_per_day' => 30,
                'nb_accounts' => 2,
            ],
            [
                'uuid' => Uuid::uuid4()->toString(),
                'name' => SubscriptionPlansEnum::STANDARD_PLAN->value,
                'price' => 5.0,
                'nb_token_per_day' => 10000,
                'nb_operations_per_day' => 100,
                'nb_accounts' => 5,
            ],
            [
                'uuid' => Uuid::uuid4()->toString(),
                'name' => SubscriptionPlansEnum::PREMIUM_PLAN->value,
                'price' => 15.0,
                'nb_token_per_day' => 20000,
                'nb_operations_per_day' => 500,
                'nb_accounts' => 10,
            ],
        ];
    }
}
