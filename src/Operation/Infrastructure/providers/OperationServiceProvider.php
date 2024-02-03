<?php

namespace App\Operation\Infrastructure\providers;

use Illuminate\Support\ServiceProvider;

class OperationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrations();
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            base_path('src/Operation/Infrastructure/database/migrations')
        );
    }
}
