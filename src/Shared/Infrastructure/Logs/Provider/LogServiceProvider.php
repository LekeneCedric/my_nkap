<?php

namespace App\Shared\Infrastructure\Logs\Provider;

use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrations();
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(base_path('src/Shared/Infrastructure/Logs/database/migrations'));
    }
}
