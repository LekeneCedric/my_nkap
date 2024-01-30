<?php

namespace App\Account\Infrastructure\Provider;

use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(
            base_path('/src/Account/Infrastructure/database/migrations')
        );
    }
}
