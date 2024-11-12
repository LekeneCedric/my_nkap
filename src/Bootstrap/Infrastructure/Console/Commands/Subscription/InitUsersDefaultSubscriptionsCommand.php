<?php

namespace App\Bootstrap\Infrastructure\Console\Commands\Subscription;

use App\User\Domain\Enums\UserStatusEnum;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class InitUsersDefaultSubscriptionsCommand extends Command
{
    protected $signature = 'subscription:subscribe-all-plan {--plan=} {--nbMonths=}';

    protected $description = 'Subscribe all users to specific plan';

    public function handle(): void
    {
        $plan = $this->option('plan');
        $nbMonths = 1;
        if (!$plan) {
            $this->error('[?] Plan is required');
            return;
        }
        if ($this->option('nbMonths')) {
            $nbMonths = (int)$this->option('nbMonths');
        }
        $subscription = DB::table('subscriptions')
            ->where('name', $plan)
            ->first();
        if (!$subscription) {
            $this->error('Plan not found');
            return;
        }
        try {
            DB::beginTransaction();
            $this->info('[+] Start subscribing all users to plan '. $plan . ' for ' . $nbMonths . ' months');
            DB::table('users')
                ->where('is_deleted', false)
                ->where('status', '=', UserStatusEnum::ACTIVE->value)
                ->get()
                ->each(function ($user) use ($nbMonths, $subscription) {
                    $currentNbAccounts = DB::table('accounts')
                        ->where('is_deleted', false)
                        ->where('user_id', $user->id)
                        ->count();
                    $userSubscription = DB::table('subscriber_subscriptions')
                        ->where('user_id', $user->id)
                        ->get();
                    if ($userSubscription->isEmpty()) {
                        DB::table('subscriber_subscriptions')
                            ->insert([
                                'uuid' => Uuid::uuid4()->toString(),
                                'user_id' => $user->id,
                                'subscription_id' => $subscription->id,
                                'start_date' => strtotime('now'),
                                'end_date' => strtotime('+' . $nbMonths . ' months'),
                                'nb_token' => $subscription->nb_token_per_day,
                                'nb_operations' => $subscription->nb_operations_per_day,
                                'nb_accounts' => max(0, $subscription->nb_accounts - $currentNbAccounts),
                                'nb_token_updated_at' => strtotime('now'),
                                'nb_operations_updated_at' => strtotime('now'),
                            ]);
                    }
                    if ($userSubscription->isNotEmpty()) {
                        DB::table('subscriber_subscriptions')
                            ->where('user_id', $user->id)
                            ->update([
                                'subscription_id' => $subscription->id,
                                'start_date' => strtotime('now'),
                                'end_date' => strtotime('+' . $nbMonths . ' months'),
                                'nb_token' => $subscription->nb_token_per_day,
                                'nb_operations' => $subscription->nb_operations_per_day,
                                'nb_accounts' => max(0, $subscription->nb_accounts - $currentNbAccounts),
                                'nb_token_updated_at' => strtotime('now'),
                                'nb_operations_updated_at' => strtotime('now'),
                            ]);
                    }
                });
            DB::commit();
            $this->info('[+] All users have been subscribed');
        } catch (Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
        }
    }

}
