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

        } catch (Exception $e) {
            return;
        }
    }
}
